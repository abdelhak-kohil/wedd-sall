<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Commune;
use App\Models\Customer;
use App\Models\Wilaya;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $query = Customer::where('business_id', $business->id)
            ->with(['wilaya', 'commune'])
            ->orderByDesc('created_at');

        // Global search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%")
                  ->orWhere('bride_name', 'ilike', "%{$search}%")
                  ->orWhere('groom_name', 'ilike', "%{$search}%")
                  ->orWhere('customer_code', 'ilike', "%{$search}%");
            });
        }

        if ($wilayaId = $request->input('wilaya_id')) {
            $query->where('wilaya_id', $wilayaId);
        }

        $customers = $query->paginate(20)->withQueryString();
        $wilayas = Wilaya::orderBy('name_fr')->get();

        return view('customers.index', compact('customers', 'wilayas', 'business'));
    }

    public function create(Request $request): View
    {
        $wilayas = Wilaya::orderBy('name_fr')->get();
        $communes = collect();

        if ($wilayaId = $request->old('wilaya_id')) {
            $communes = Commune::where('wilaya_id', $wilayaId)->orderBy('name_fr')->get();
        }

        return view('customers.create', compact('wilayas', 'communes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'second_phone' => 'nullable|string|max:30',
            'whatsapp_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'wilaya_id' => 'nullable|exists:wilayas,id',
            'commune_id' => 'nullable|exists:communes,id',
            'address_line' => 'nullable|string|max:255',
            'bride_name' => 'nullable|string|max:120',
            'groom_name' => 'nullable|string|max:120',
            'preferred_language' => 'in:fr,ar',
            'source' => 'in:FACEBOOK,INSTAGRAM,TIKTOK,GOOGLE,RECOMMENDATION,WALK_IN,OTHER',
            'internal_notes' => 'nullable|string',
        ]);

        $business = $request->user()->business ?? Business::first();

        $code = 'CLT-' . strtoupper(uniqid());

        $customer = Customer::create(array_merge($validated, [
            'business_id' => $business->id,
            'customer_code' => $code,
        ]));

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Client créé avec succès. Code: ' . $code);
    }

    public function show(Customer $customer): View
    {
        $customer->load(['wilaya', 'commune', 'reservations.space', 'leads']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $wilayas = Wilaya::orderBy('name_fr')->get();
        $communes = $customer->wilaya_id
            ? Commune::where('wilaya_id', $customer->wilaya_id)->orderBy('name_fr')->get()
            : collect();

        return view('customers.edit', compact('customer', 'wilayas', 'communes'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'second_phone' => 'nullable|string|max:30',
            'whatsapp_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'wilaya_id' => 'nullable|exists:wilayas,id',
            'commune_id' => 'nullable|exists:communes,id',
            'address_line' => 'nullable|string|max:255',
            'bride_name' => 'nullable|string|max:120',
            'groom_name' => 'nullable|string|max:120',
            'preferred_language' => 'in:fr,ar',
            'source' => 'in:FACEBOOK,INSTAGRAM,TIKTOK,GOOGLE,RECOMMENDATION,WALK_IN,OTHER',
            'internal_notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Dossier client mis à jour.');
    }

    // AJAX: get communes for a given wilaya
    public function communes(Wilaya $wilaya)
    {
        return response()->json(
            $wilaya->communes()->orderBy('name_fr')->get(['id', 'name_fr', 'name_ar'])
        );
    }
}
