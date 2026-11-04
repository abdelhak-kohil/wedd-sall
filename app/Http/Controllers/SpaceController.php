<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\TimeSlot;
use App\Models\Venue;
use App\Models\VenueSpace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpaceController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $venues = Venue::where('business_id', $business->id)->with('spaces')->get();
        $spaces = VenueSpace::where('business_id', $business->id)->with('venue')->get();
        $slots = TimeSlot::where('business_id', $business->id)->orderBy('sort_order')->get();

        return view('settings.spaces', compact('business', 'venues', 'spaces', 'slots'));
    }

    public function storeSpace(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'name_fr' => 'required|string|max:150',
            'name_ar' => 'required|string|max:150',
            'capacity_min' => 'required|integer|min:10',
            'capacity_max' => 'required|integer|gte:capacity_min',
            'base_price' => 'required|numeric|min:0',
        ]);

        $business = $request->user()->business ?? Business::first();

        VenueSpace::create([
            'business_id' => $business->id,
            'venue_id' => $validated['venue_id'],
            'name_fr' => $validated['name_fr'],
            'name_ar' => $validated['name_ar'],
            'capacity_min' => $validated['capacity_min'],
            'capacity_max' => $validated['capacity_max'],
            'base_price' => $validated['base_price'],
            'is_active' => true,
        ]);

        return redirect()->route('settings.spaces')->with('success', 'Espace ajouté avec succès.');
    }

    public function storeSlot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_fr' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_overnight' => 'boolean',
        ]);

        $business = $request->user()->business ?? Business::first();

        TimeSlot::create([
            'business_id' => $business->id,
            'name_fr' => $validated['name_fr'],
            'name_ar' => $validated['name_ar'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_overnight' => $request->boolean('is_overnight'),
            'sort_order' => TimeSlot::where('business_id', $business->id)->count() + 1,
            'is_active' => true,
        ]);

        return redirect()->route('settings.spaces')->with('success', 'Créneau horaire ajouté avec succès.');
    }
}
