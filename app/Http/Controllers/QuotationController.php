<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\VenueSpace;
use App\Services\AvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $query = Quotation::where('business_id', $business->id)
            ->with(['space', 'package', 'customer'])
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($q = $request->input('q')) {
            $likeOp = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($subq) use ($q, $likeOp) {
                $subq->where('quote_number', $likeOp, "%{$q}%")
                     ->orWhere('customer_name', $likeOp, "%{$q}%")
                     ->orWhere('customer_phone', $likeOp, "%{$q}%");
            });
        }

        $quotations = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Quotation::where('business_id', $business->id)->count(),
            'sent' => Quotation::where('business_id', $business->id)->where('status', Quotation::STATUS_SENT)->count(),
            'accepted' => Quotation::where('business_id', $business->id)->where('status', Quotation::STATUS_ACCEPTED)->count(),
            'potential_revenue' => Quotation::where('business_id', $business->id)->whereIn('status', [Quotation::STATUS_SENT, Quotation::STATUS_DRAFT])->sum('total_price'),
        ];

        return view('quotations.index', compact('quotations', 'business', 'stats'));
    }

    public function create(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();
        $spaces = VenueSpace::where('business_id', $business->id)->where('is_active', true)->get();
        $packages = Package::where('business_id', $business->id)->where('is_active', true)->orderBy('base_price')->get();
        $slots = TimeSlot::where('business_id', $business->id)->where('is_active', true)->orderBy('sort_order')->get();

        $foundCustomer = null;
        if ($searchPhone = $request->input('phone')) {
            $foundCustomer = Customer::where('business_id', $business->id)->where('phone', $searchPhone)->first();
        }

        return view('quotations.create', compact('spaces', 'packages', 'slots', 'business', 'foundCustomer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:30',
            'bride_name' => 'nullable|string|max:120',
            'groom_name' => 'nullable|string|max:120',
            'venue_space_id' => 'required|exists:venue_spaces,id',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'expected_guests' => 'required|integer|min:1',
            'package_id' => 'nullable|exists:packages,id',
            'items' => 'nullable|array',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.total' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:none,fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'total_price' => 'required|numeric|min:0',
            'deposit_required' => 'required|numeric|min:0',
            'valid_days' => 'nullable|integer|min:1|max:60',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $business = $request->user()->business ?? Business::first();

        // Process line items & professional calculation
        $rawItems = $request->input('items', []);
        $processedItems = [];
        $calculatedSubtotal = 0;

        if (is_array($rawItems) && count($rawItems) > 0) {
            foreach ($rawItems as $item) {
                $desc = trim($item['description'] ?? '');
                $qty = (float)($item['quantity'] ?? 1);
                $price = (float)($item['unit_price'] ?? 0);
                if ($desc !== '' && $qty > 0) {
                    $lineTotal = round($qty * $price, 2);
                    $calculatedSubtotal += $lineTotal;
                    $processedItems[] = [
                        'description' => $desc,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'total' => $lineTotal,
                    ];
                }
            }
        }

        // If items were empty, fallback to space/package or total_price
        if (empty($processedItems)) {
            $space = VenueSpace::find($validated['venue_space_id']);
            $package = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;
            $desc = $package ? ('Forfait ' . $package->name_fr) : ('Location ' . ($space ? $space->name : 'Salle'));
            $price = (float)$validated['total_price'];
            $calculatedSubtotal = $price;
            $processedItems[] = [
                'description' => $desc,
                'quantity' => 1,
                'unit_price' => $price,
                'total' => $price,
            ];
        }

        $subtotal = $calculatedSubtotal;
        $discountType = $validated['discount_type'] ?? 'none';
        $discountValue = (float)($validated['discount_value'] ?? 0);
        $discountAmount = 0;

        if ($discountType === 'percent') {
            $discountAmount = round($subtotal * ($discountValue / 100), 2);
        } elseif ($discountType === 'fixed') {
            $discountAmount = min($subtotal, $discountValue);
        } else {
            $discountType = 'none';
            $discountValue = 0;
            $discountAmount = 0;
        }

        $netHt = max(0, $subtotal - $discountAmount);
        $taxRate = (float)($validated['tax_rate'] ?? 0);
        $taxAmount = round($netHt * ($taxRate / 100), 2);
        $calculatedTotal = $netHt + $taxAmount;

        // Use calculated total if items were submitted, else fallback to validated total_price
        $finalTotal = !empty($request->input('items')) ? $calculatedTotal : (float)$validated['total_price'];

        $count = Quotation::where('business_id', $business->id)->count() + 1;
        $quoteNumber = 'DEV-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validUntil = now()->addDays((int) ($validated['valid_days'] ?? 15));

        $quotation = Quotation::create([
            'business_id' => $business->id,
            'quote_number' => $quoteNumber,
            'customer_id' => $validated['customer_id'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'bride_name' => $validated['bride_name'] ?? null,
            'groom_name' => $validated['groom_name'] ?? null,
            'venue_space_id' => $validated['venue_space_id'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'expected_guests' => $validated['expected_guests'],
            'package_id' => $validated['package_id'] ?? null,
            'subtotal' => $subtotal,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'items' => $processedItems,
            'total_price' => $finalTotal,
            'deposit_required' => $validated['deposit_required'],
            'status' => Quotation::STATUS_SENT,
            'valid_until' => $validUntil,
            'notes' => $validated['notes'] ?? null,
            'payment_terms' => $validated['payment_terms'] ?? null,
            'created_by' => auth()->id(),
        ]);


        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Devis N° ' . $quoteNumber . ' généré avec succès.');
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load(['space.venue', 'package', 'customer', 'creator', 'reservation']);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation): View
    {
        $business = request()->user()->business ?? Business::first();
        $spaces = VenueSpace::where('business_id', $business->id)->where('is_active', true)->get();
        $packages = Package::where('business_id', $business->id)->where('is_active', true)->orderBy('base_price')->get();
        $slots = TimeSlot::where('business_id', $business->id)->where('is_active', true)->orderBy('sort_order')->get();
        $quotation->load(['space', 'package', 'customer']);

        return view('quotations.edit', compact('quotation', 'spaces', 'packages', 'slots', 'business'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:30',
            'bride_name' => 'nullable|string|max:120',
            'groom_name' => 'nullable|string|max:120',
            'venue_space_id' => 'required|exists:venue_spaces,id',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'expected_guests' => 'required|integer|min:1',
            'package_id' => 'nullable|exists:packages,id',
            'items' => 'nullable|array',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.total' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:none,fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'total_price' => 'required|numeric|min:0',
            'deposit_required' => 'required|numeric|min:0',
            'valid_until' => 'nullable|date',
            'status' => 'nullable|string|in:DRAFT,SENT,ACCEPTED,EXPIRED,DECLINED',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        // Process line items & professional calculation
        $rawItems = $request->input('items', []);
        $processedItems = [];
        $calculatedSubtotal = 0;

        if (is_array($rawItems) && count($rawItems) > 0) {
            foreach ($rawItems as $item) {
                $desc = trim($item['description'] ?? '');
                $qty = (float)($item['quantity'] ?? 1);
                $price = (float)($item['unit_price'] ?? 0);
                if ($desc !== '' && $qty > 0) {
                    $lineTotal = round($qty * $price, 2);
                    $calculatedSubtotal += $lineTotal;
                    $processedItems[] = [
                        'description' => $desc,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'total' => $lineTotal,
                    ];
                }
            }
        }

        if (empty($processedItems)) {
            $space = VenueSpace::find($validated['venue_space_id']);
            $package = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;
            $desc = $package ? ('Forfait ' . $package->name_fr) : ('Location ' . ($space ? $space->name : 'Salle'));
            $price = (float)$validated['total_price'];
            $calculatedSubtotal = $price;
            $processedItems[] = [
                'description' => $desc,
                'quantity' => 1,
                'unit_price' => $price,
                'total' => $price,
            ];
        }

        $subtotal = $calculatedSubtotal;
        $discountType = $validated['discount_type'] ?? 'none';
        $discountValue = (float)($validated['discount_value'] ?? 0);
        $discountAmount = 0;

        if ($discountType === 'percent') {
            $discountAmount = round($subtotal * ($discountValue / 100), 2);
        } elseif ($discountType === 'fixed') {
            $discountAmount = min($subtotal, $discountValue);
        } else {
            $discountType = 'none';
            $discountValue = 0;
            $discountAmount = 0;
        }

        $netHt = max(0, $subtotal - $discountAmount);
        $taxRate = (float)($validated['tax_rate'] ?? 0);
        $taxAmount = round($netHt * ($taxRate / 100), 2);
        $calculatedTotal = $netHt + $taxAmount;

        $finalTotal = !empty($request->input('items')) ? $calculatedTotal : (float)$validated['total_price'];
        $validUntil = !empty($validated['valid_until']) ? $validated['valid_until'] : $quotation->valid_until;

        $quotation->update([
            'customer_id' => $validated['customer_id'] ?? $quotation->customer_id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'bride_name' => $validated['bride_name'] ?? null,
            'groom_name' => $validated['groom_name'] ?? null,
            'venue_space_id' => $validated['venue_space_id'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'expected_guests' => $validated['expected_guests'],
            'package_id' => $validated['package_id'] ?? null,
            'subtotal' => $subtotal,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'items' => $processedItems,
            'total_price' => $finalTotal,
            'deposit_required' => $validated['deposit_required'],
            'status' => $validated['status'] ?? $quotation->status,
            'valid_until' => $validUntil,
            'notes' => $validated['notes'] ?? null,
            'payment_terms' => $validated['payment_terms'] ?? null,
        ]);

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Devis N° ' . $quotation->quote_number . ' mis à jour avec succès.');
    }

    public function destroy(Request $request, Quotation $quotation): RedirectResponse
    {
        $user = $request->user();
        if (!$user || (!$user->isOwner() && !$user->isManager())) {
            abort(403, 'Action non autorisée. Seuls le gérant et le propriétaire ont l\'autorisation de supprimer un devis.');
        }

        if ($quotation->status === Quotation::STATUS_ACCEPTED && $quotation->converted_reservation_id) {
            return back()->with('error', 'Impossible de supprimer un devis déjà converti en réservation.');
        }

        $quoteNumber = $quotation->quote_number;
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Devis ' . $quoteNumber . ' supprimé avec succès.');
    }

    public function print(Quotation $quotation): View
    {
        $quotation->load(['space.venue', 'package', 'customer']);

        return view('quotations.print', compact('quotation'));
    }

    public function convert(Request $request, Quotation $quotation, AvailabilityService $availability): RedirectResponse
    {
        if ($quotation->status === Quotation::STATUS_ACCEPTED && $quotation->converted_reservation_id) {
            return redirect()->route('reservations.show', $quotation->converted_reservation_id);
        }

        // Check availability
        $check = $availability->checkSpaceAvailability(
            $quotation->space,
            $quotation->event_date->toDateString(),
            $quotation->start_time,
            $quotation->end_time
        );

        if (!$check['available'] && $check['status'] !== 'EXPIRED_HOLD') {
            return back()->withErrors(['error' => 'Impossible de convertir le devis : ' . $check['message']]);
        }

        $reservation = DB::transaction(function () use ($quotation, $request) {
            $business = $quotation->business;
            $count = Reservation::where('business_id', $business->id)->count() + 1;
            $resNumber = 'RES-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Create customer if needed
            $customerId = $quotation->customer_id;
            if (!$customerId) {
                $code = 'CLT-' . strtoupper(uniqid());
                $customer = Customer::create([
                    'business_id' => $business->id,
                    'customer_code' => $code,
                    'full_name' => $quotation->customer_name,
                    'phone' => $quotation->customer_phone,
                    'source' => 'WALK_IN',
                    'bride_name' => $quotation->bride_name,
                    'groom_name' => $quotation->groom_name,
                ]);
                $customerId = $customer->id;
            }

            $reservation = Reservation::create([
                'business_id' => $business->id,
                'reservation_number' => $resNumber,
                'customer_id' => $customerId,
                'venue_space_id' => $quotation->venue_space_id,
                'package_id' => $quotation->package_id,
                'customer_name' => $quotation->customer_name,
                'customer_phone' => $quotation->customer_phone,
                'bride_name' => $quotation->bride_name,
                'groom_name' => $quotation->groom_name,
                'event_type' => 'Mariage',
                'event_date' => $quotation->event_date,
                'start_time' => $quotation->start_time,
                'end_time' => $quotation->end_time,
                'expected_guests' => $quotation->expected_guests,
                'status' => Reservation::STATUS_CONFIRMED,
                'payment_status' => Reservation::PAYMENT_UNPAID,
                'total_price' => $quotation->total_price,
                'deposit_required' => $quotation->deposit_required,
                'deposit_paid' => 0,
                'total_paid' => 0,
                'remaining_balance' => $quotation->total_price,
                'internal_notes' => 'Converti depuis le devis ' . $quotation->quote_number . ($quotation->notes ? ' : ' . $quotation->notes : ''),
                'created_by' => auth()->id(),
            ]);

            $quotation->update([
                'status' => Quotation::STATUS_ACCEPTED,
                'converted_reservation_id' => $reservation->id,
            ]);

            return $reservation;
        });

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Devis converti en réservation N° ' . $reservation->reservation_number);
    }
}
