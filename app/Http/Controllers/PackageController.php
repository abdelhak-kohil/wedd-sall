<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();
        $packages = Package::where('business_id', $business->id)->orderBy('base_price')->get();

        return view('packages.index', compact('packages', 'business'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_fr' => 'required|string|max:150',
            'name_ar' => 'nullable|string|max:150',
            'description_fr' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'price_per_guest' => 'nullable|numeric|min:0',
            'included_items_text' => 'nullable|string',
        ]);

        $business = $request->user()->business ?? Business::first();

        // Convert newline-separated items to array
        $items = [];
        if (!empty($validated['included_items_text'])) {
            $items = array_values(array_filter(array_map('trim', explode("\n", $validated['included_items_text']))));
        }

        Package::create([
            'business_id' => $business->id,
            'name_fr' => $validated['name_fr'],
            'name_ar' => $validated['name_ar'] ?? null,
            'description_fr' => $validated['description_fr'] ?? null,
            'base_price' => $validated['base_price'],
            'price_per_guest' => $validated['price_per_guest'] ?? 0,
            'included_items' => $items,
            'is_active' => true,
        ]);

        return back()->with('success', 'Forfait créé avec succès.');
    }

    public function toggle(Package $package): RedirectResponse
    {
        $package->update(['is_active' => !$package->is_active]);

        return back()->with('success', 'Statut du forfait mis à jour.');
    }
}
