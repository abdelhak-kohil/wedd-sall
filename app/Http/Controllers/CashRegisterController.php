<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\CashRegister;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $activeRegister = CashRegister::where('business_id', $business->id)
            ->where('status', CashRegister::STATUS_OPEN)
            ->latest()
            ->first();

        $registers = CashRegister::where('business_id', $business->id)
            ->with(['opener', 'closer'])
            ->orderByDesc('register_date')
            ->orderByDesc('id')
            ->paginate(15);

        // Today's cash payments
        $todayCashPayments = collect();
        if ($activeRegister) {
            $todayCashPayments = Payment::where('cash_register_id', $activeRegister->id)
                ->with(['reservation', 'receiver'])
                ->orderByDesc('created_at')
                ->get();
        }

        return view('cash_registers.index', compact('activeRegister', 'registers', 'todayCashPayments', 'business'));
    }

    public function open(Request $request): RedirectResponse
    {
        $business = $request->user()->business ?? Business::first();

        // Check if already an open register
        $existing = CashRegister::where('business_id', $business->id)
            ->where('status', CashRegister::STATUS_OPEN)
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'Une caisse est déjà ouverte pour aujourd\'hui.']);
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $opening = (float) $validated['opening_balance'];

        CashRegister::create([
            'business_id' => $business->id,
            'register_date' => now()->toDateString(),
            'opening_balance' => $opening,
            'total_cash_in' => 0,
            'total_cash_out' => 0,
            'expected_closing_balance' => $opening,
            'status' => CashRegister::STATUS_OPEN,
            'opened_by' => auth()->id(),
            'opened_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Caisse ouverte avec un fond initial de ' . number_format($opening, 2, ',', ' ') . ' DA.');
    }

    public function close(Request $request, CashRegister $register): RedirectResponse
    {
        $validated = $request->validate([
            'actual_closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $register->update([
            'actual_closing_balance' => $validated['actual_closing_balance'],
            'status' => CashRegister::STATUS_CLOSED,
            'closed_by' => auth()->id(),
            'closed_at' => now(),
            'notes' => $register->notes . ($validated['notes'] ? "\nClôture : " . $validated['notes'] : ''),
        ]);

        $ecart = $register->actual_closing_balance - $register->expected_closing_balance;
        $msg = 'Caisse clôturée avec succès.';
        if ($ecart != 0) {
            $msg .= ' Écart constaté : ' . ($ecart > 0 ? '+' : '') . number_format($ecart, 2, ',', ' ') . ' DA.';
        }

        return back()->with('success', $msg);
    }
}
