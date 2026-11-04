<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $query = Payment::where('business_id', $business->id)
            ->with(['reservation.space', 'receiver'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        if ($method = $request->input('method')) {
            $query->where('payment_method', $method);
        }

        if ($type = $request->input('type')) {
            $query->where('payment_type', $type);
        }

        if ($q = $request->input('q')) {
            $query->where(function ($subq) use ($q) {
                $subq->where('receipt_number', 'ilike', "%{$q}%")
                     ->orWhereHas('reservation', function ($resQ) use ($q) {
                         $resQ->where('customer_name', 'ilike', "%{$q}%")
                              ->orWhere('reservation_number', 'ilike', "%{$q}%")
                              ->orWhere('customer_phone', 'ilike', "%{$q}%");
                     });
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        // Summary stats
        $totalCollected = Payment::where('business_id', $business->id)
            ->where('payment_type', '!=', Payment::TYPE_REFUND)
            ->sum('amount');
        $cashCollected = Payment::where('business_id', $business->id)
            ->where('payment_method', Payment::METHOD_CASH)
            ->where('payment_type', '!=', Payment::TYPE_REFUND)
            ->sum('amount');

        return view('payments.index', compact('payments', 'business', 'totalCollected', 'cashCollected'));
    }

    public function store(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:DEPOSIT,INSTALLMENT,FINAL_BALANCE,REFUND',
            'payment_method' => 'required|in:CASH,CHEQUE,TRANSFER,OTHER',
            'payment_date' => 'required|date',
            'cheque_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $business = $reservation->business;

        $payment = DB::transaction(function () use ($validated, $reservation, $business) {
            $count = Payment::where('business_id', $business->id)->count() + 1;
            $receiptNumber = 'REC-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Check if active cash register exists for cash payments
            $registerId = null;
            if ($validated['payment_method'] === Payment::METHOD_CASH) {
                $activeRegister = CashRegister::where('business_id', $business->id)
                    ->where('status', CashRegister::STATUS_OPEN)
                    ->latest()
                    ->first();

                if ($activeRegister) {
                    $registerId = $activeRegister->id;
                    if ($validated['payment_type'] === Payment::TYPE_REFUND) {
                        $activeRegister->increment('total_cash_out', $validated['amount']);
                    } else {
                        $activeRegister->increment('total_cash_in', $validated['amount']);
                    }
                    $activeRegister->update(['expected_closing_balance' => $activeRegister->calculateExpected()]);
                }
            }

            $payment = Payment::create([
                'business_id' => $business->id,
                'reservation_id' => $reservation->id,
                'cash_register_id' => $registerId,
                'receipt_number' => $receiptNumber,
                'payment_type' => $validated['payment_type'],
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'cheque_number' => $validated['cheque_number'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
                'received_by' => auth()->id(),
            ]);

            // Sync financials on reservation
            $reservation->syncFinancials();

            // Auto-promote temporary hold to confirmed if deposit is paid
            if ($reservation->isTemporaryHold() && $validated['payment_type'] === Payment::TYPE_DEPOSIT) {
                $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);
            }

            // Audit Log
            AuditLog::create([
                'business_id' => $business->id,
                'user_id' => auth()->id(),
                'action' => 'PAYMENT_RECEIVED',
                'auditable_type' => Payment::class,
                'auditable_id' => $payment->id,
                'new_values' => [
                    'receipt_number' => $payment->receipt_number,
                    'amount' => $payment->amount,
                    'method' => $payment->payment_method,
                    'reservation_number' => $reservation->reservation_number,
                ],
                'ip_address' => request()->ip(),
            ]);

            return $payment;
        });

        return back()->with('success', 'Paiement enregistré avec succès. Reçu N° ' . $payment->receipt_number)
            ->with('last_payment_id', $payment->id);
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['business', 'reservation.space', 'reservation.customer', 'receiver']);

        return view('payments.receipt', compact('payment'));
    }
}
