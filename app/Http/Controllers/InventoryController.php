<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $query = InventoryItem::where('business_id', $business->id)
            ->with(['movements' => fn ($q) => $q->latest()->limit(1)])
            ->orderBy('category')
            ->orderBy('name');

        if ($cat = $request->input('category')) {
            $query->where('category', $cat);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('quantity_available', '<=', 'quantity_alert');
        }

        if ($q = $request->input('q')) {
            $op = \DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($sub) use ($q, $op) {
                $sub->where('name', $op, "%{$q}%")
                    ->orWhere('location', $op, "%{$q}%");
            });
        }

        $items = $query->get();

        // KPI stats
        $allItems = InventoryItem::where('business_id', $business->id)->get();
        $stats = [
            'total_items'   => $allItems->count(),
            'total_value'   => $allItems->sum(fn ($i) => ($i->unit_cost ?? 0) * $i->quantity_total),
            'low_stock'     => $allItems->filter(fn ($i) => $i->is_low_stock && !$i->is_out_of_stock)->count(),
            'out_of_stock'  => $allItems->filter(fn ($i) => $i->is_out_of_stock)->count(),
        ];

        // Recent movements (last 20)
        $recentMovements = InventoryMovement::where('business_id', $business->id)
            ->with(['item', 'creator', 'reservation'])
            ->latest()
            ->limit(20)
            ->get();

        $categories = InventoryItem::CATEGORIES;

        return view('inventory.index', compact(
            'items',
            'stats',
            'recentMovements',
            'categories',
            'business'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:150',
            'category'           => 'required|in:' . implode(',', array_keys(InventoryItem::CATEGORIES)),
            'unit'               => 'required|string|max:20',
            'quantity_total'     => 'required|integer|min:0',
            'quantity_available' => 'nullable|integer|min:0',
            'quantity_alert'     => 'nullable|integer|min:0',
            'description'        => 'nullable|string',
            'location'           => 'nullable|string|max:100',
            'unit_cost'          => 'nullable|numeric|min:0',
        ]);

        $business = $request->user()->business ?? Business::first();
        $total    = (int) $validated['quantity_total'];
        $avail    = isset($validated['quantity_available']) ? min($total, (int) $validated['quantity_available']) : $total;

        $item = InventoryItem::create([
            'business_id'        => $business->id,
            'name'               => $validated['name'],
            'category'           => $validated['category'],
            'unit'               => $validated['unit'],
            'quantity_total'     => $total,
            'quantity_available' => $avail,
            'quantity_alert'     => $validated['quantity_alert'] ?? 5,
            'description'        => $validated['description'] ?? null,
            'location'           => $validated['location'] ?? null,
            'unit_cost'          => $validated['unit_cost'] ?? 0,
            'is_active'          => true,
            'created_by'         => auth()->id(),
        ]);

        // Record initial intake movement
        InventoryMovement::create([
            'business_id'        => $business->id,
            'inventory_item_id'  => $item->id,
            'type'               => InventoryMovement::TYPE_IN,
            'quantity'           => $total,
            'quantity_before'    => 0,
            'quantity_after'     => $avail,
            'reason'             => 'Création initiale de l\'article au catalogue',
            'created_by'         => auth()->id(),
        ]);

        return back()->with('success', 'Article « ' . $item->name . ' » ajouté au stock avec succès.');
    }

    public function update(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorizeItem($item, $request);

        $validated = $request->validate([
            'name'               => 'required|string|max:150',
            'category'           => 'required|in:' . implode(',', array_keys(InventoryItem::CATEGORIES)),
            'unit'               => 'required|string|max:20',
            'quantity_total'     => 'required|integer|min:0',
            'quantity_available' => 'required|integer|min:0',
            'quantity_alert'     => 'nullable|integer|min:0',
            'description'        => 'nullable|string',
            'location'           => 'nullable|string|max:100',
            'unit_cost'          => 'nullable|numeric|min:0',
        ]);

        $beforeTotal = $item->quantity_total;
        $beforeAvail = $item->quantity_available;

        // Ensure total is at least equal to available
        if ($validated['quantity_available'] > $validated['quantity_total']) {
            $validated['quantity_total'] = $validated['quantity_available'];
        }

        $item->update($validated);

        // If either quantity changed, record an audit movement
        if ($beforeAvail !== (int)$item->quantity_available || $beforeTotal !== (int)$item->quantity_total) {
            $diff = (int)$item->quantity_available - $beforeAvail;
            $business = $request->user()->business ?? Business::first();

            InventoryMovement::create([
                'business_id'        => $business->id,
                'inventory_item_id'  => $item->id,
                'type'               => InventoryMovement::TYPE_ADJUST,
                'quantity'           => abs($diff) ?: abs((int)$item->quantity_total - $beforeTotal),
                'quantity_before'    => $beforeAvail,
                'quantity_after'     => (int)$item->quantity_available,
                'reason'             => 'Mise à jour directe fiche matériel (Total: ' . $beforeTotal . ' → ' . $item->quantity_total . ', Dispo: ' . $beforeAvail . ' → ' . $item->quantity_available . ')',
                'created_by'         => auth()->id(),
            ]);
        }

        return back()->with('success', 'Article « ' . $item->name . ' » mis à jour avec succès.');
    }

    public function destroy(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorizeItem($item, $request);

        $user = $request->user();
        if (!$user->isOwner() && !$user->isManager()) {
            abort(403);
        }

        $item->delete();

        return back()->with('success', 'Article supprimé du stock.');
    }

    /**
     * Record a stock movement (IN / OUT / ADJUST / RESERVE / RETURN).
     */
    public function move(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorizeItem($item, $request);

        $validated = $request->validate([
            'type'           => 'required|in:IN,OUT,ADJUST,RESERVE,RETURN',
            'quantity'       => 'required|integer|min:1',
            'new_quantity'   => 'nullable|integer|min:0', // used only for ADJUST
            'reason'         => 'nullable|string|max:200',
            'notes'          => 'nullable|string',
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        $before = $item->quantity_available;

        switch ($validated['type']) {
            case InventoryMovement::TYPE_IN:
                $after = $before + $validated['quantity'];
                $item->increment('quantity_total', $validated['quantity']);
                $item->increment('quantity_available', $validated['quantity']);
                break;

            case InventoryMovement::TYPE_OUT:
                $after = max(0, $before - $validated['quantity']);
                $item->decrement('quantity_total', $validated['quantity']);
                $item->quantity_available = $after;
                $item->save();
                break;

            case InventoryMovement::TYPE_RETURN:
                $after = $before + $validated['quantity'];
                $item->increment('quantity_available', $validated['quantity']);
                break;

            case InventoryMovement::TYPE_RESERVE:
                $after = max(0, $before - $validated['quantity']);
                $item->quantity_available = $after;
                $item->save();
                break;

            case InventoryMovement::TYPE_ADJUST:
                // ADJUST sets quantity_available to an absolute new value
                $newQty = (int) $request->input('new_quantity', $before);
                $after  = $newQty;
                $item->quantity_available = $newQty;
                $item->quantity_total     = $newQty; // sync total on full recount
                $item->save();
                break;

            default:
                $after = $before;
        }

        $business = $request->user()->business ?? Business::first();

        InventoryMovement::create([
            'business_id'        => $business->id,
            'inventory_item_id'  => $item->id,
            'reservation_id'     => $validated['reservation_id'] ?? null,
            'type'               => $validated['type'],
            'quantity'           => $validated['quantity'],
            'quantity_before'    => $before,
            'quantity_after'     => $after,
            'reason'             => $validated['reason'] ?? null,
            'notes'              => $validated['notes'] ?? null,
            'created_by'         => auth()->id(),
        ]);

        return back()->with('success', 'Mouvement de stock enregistré.');
    }

    /** Quick +1 / -1 stock action from card buttons */
    public function quickstock(Request $request, InventoryItem $item): RedirectResponse
    {
        $this->authorizeItem($item, $request);

        $type = strtoupper($request->input('type', 'IN')); // IN or OUT
        $qty  = max(1, (int) $request->input('qty', 1));

        $before = $item->quantity_available;

        if ($type === 'IN') {
            $after = $before + $qty;
            $item->increment('quantity_available', $qty);
            $item->increment('quantity_total', $qty);
        } else {
            $after = max(0, $before - $qty);
            $item->quantity_available = $after;
            $item->save();
        }

        $business = $request->user()->business ?? \App\Models\Business::first();

        \App\Models\InventoryMovement::create([
            'business_id'       => $business->id,
            'inventory_item_id' => $item->id,
            'type'              => $type,
            'quantity'          => $qty,
            'quantity_before'   => $before,
            'quantity_after'    => $after,
            'reason'            => 'Ajustement rapide (+/- 1)',
            'created_by'        => auth()->id(),
        ]);

        return back()->with('success', ($type === 'IN' ? '+1' : '-1') . ' enregistré pour « ' . $item->name . ' ».');
    }

    // ── Private helpers ─────────────────────────────────────────────────────

    private function authorizeItem(InventoryItem $item, Request $request): void
    {
        $business = $request->user()->business ?? Business::first();
        if ($item->business_id !== $business->id) {
            abort(403);
        }
    }
}
