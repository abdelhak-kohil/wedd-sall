<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    // ── Index ────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $business   = $request->user()->business ?? Business::first();
        $categories = ExpenseCategory::where('business_id', $business->id)
            ->where('is_active', true)->orderBy('name_fr')->get();

        $query = Expense::where('business_id', $business->id)
            ->with(['category', 'creator'])
            ->orderByDesc('expense_date')
            ->orderByDesc('id');

        if ($catId = $request->input('category')) {
            $query->where('expense_category_id', $catId);
        }
        if ($method = $request->input('method')) {
            $query->where('payment_method', $method);
        }
        if ($month = $request->input('month')) {          // YYYY-MM
            $query->whereRaw("to_char(expense_date,'YYYY-MM') = ?", [$month]);
        }
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('description', 'ilike', "%{$q}%")
                    ->orWhere('vendor', 'ilike', "%{$q}%")
                    ->orWhere('reference', 'ilike', "%{$q}%");
            });
        }

        $expenses   = $query->paginate(20)->withQueryString();
        $totalMonth = Expense::where('business_id', $business->id)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalMonth', 'business'));
    }

    // ── Create form ──────────────────────────────────────────────
    public function create(Request $request): View
    {
        $business   = $request->user()->business ?? Business::first();
        $categories = ExpenseCategory::where('business_id', $business->id)
            ->where('is_active', true)->orderBy('name_fr')->get();

        return view('expenses.create', compact('categories', 'business'));
    }

    // ── Store ────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $business = $request->user()->business ?? Business::first();

        $validated = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'description'         => 'required|string|max:500',
            'vendor'              => 'nullable|string|max:150',
            'amount'              => 'required|numeric|min:1',
            'expense_date'        => 'required|date',
            'payment_method'      => 'required|in:CASH,CHEQUE,TRANSFER,OTHER',
            'cheque_number'       => 'nullable|string|max:100',
            'bank_name'           => 'nullable|string|max:100',
            'reference'           => 'nullable|string|max:50',
            'notes'               => 'nullable|string',
        ]);

        $expense = Expense::create(array_merge($validated, [
            'business_id' => $business->id,
            'created_by'  => auth()->id(),
        ]));

        AuditLog::create([
            'business_id'    => $business->id,
            'user_id'        => auth()->id(),
            'action'         => 'EXPENSE_CREATED',
            'auditable_type' => Expense::class,
            'auditable_id'   => $expense->id,
            'new_values'     => [
                'description' => $expense->description,
                'amount'      => $expense->amount,
                'date'        => $expense->expense_date->toDateString(),
            ],
            'ip_address'     => $request->ip(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense enregistrée : ' . number_format($expense->amount, 2, ',', ' ') . ' DA');
    }

    // ── Profitability report ─────────────────────────────────────
    public function report(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();
        $year     = (int) $request->input('year', now()->year);
        $driver   = DB::connection()->getDriverName(); // pgsql | sqlite

        // Helper: month extraction SQL fragment per driver
        $monthFn = fn(string $col) => $driver === 'pgsql'
            ? "EXTRACT(MONTH FROM {$col})::int"
            : "CAST(strftime('%m', {$col}) AS INTEGER)";

        $yearFn = fn(string $col) => $driver === 'pgsql'
            ? "EXTRACT(YEAR FROM {$col})::int"
            : "CAST(strftime('%Y', {$col}) AS INTEGER)";

        // Revenue: all non-refund payments for the year
        $revenueByMonth = Payment::where('business_id', $business->id)
            ->where('payment_type', '!=', Payment::TYPE_REFUND)
            ->whereYear('payment_date', $year)
            ->selectRaw("{$monthFn('payment_date')} AS month, SUM(amount) AS total")
            ->groupBy('month')
            ->pluck('total', 'month');

        // Refunds
        $refundsByMonth = Payment::where('business_id', $business->id)
            ->where('payment_type', Payment::TYPE_REFUND)
            ->whereYear('payment_date', $year)
            ->selectRaw("{$monthFn('payment_date')} AS month, SUM(amount) AS total")
            ->groupBy('month')
            ->pluck('total', 'month');

        // Expenses
        $expensesByMonth = Expense::where('business_id', $business->id)
            ->whereYear('expense_date', $year)
            ->selectRaw("{$monthFn('expense_date')} AS month, SUM(amount) AS total")
            ->groupBy('month')
            ->pluck('total', 'month');

        // Expenses by category (year)
        $byCategory = Expense::where('business_id', $business->id)
            ->whereYear('expense_date', $year)
            ->with('category')
            ->selectRaw('expense_category_id, SUM(amount) AS total')
            ->groupBy('expense_category_id')
            ->get();

        // Reservations count
        $reservationsByMonth = Reservation::where('business_id', $business->id)
            ->whereYear('event_date', $year)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED])
            ->selectRaw("{$monthFn('event_date')} AS month, COUNT(*) AS total")
            ->groupBy('month')
            ->pluck('total', 'month');

        // Build 12-month table
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue  = (float) ($revenueByMonth[$m]  ?? 0);
            $refunds  = (float) ($refundsByMonth[$m]  ?? 0);
            $expenses = (float) ($expensesByMonth[$m] ?? 0);
            $months[$m] = [
                'revenue'      => $revenue,
                'refunds'      => $refunds,
                'net_revenue'  => $revenue - $refunds,
                'expenses'     => $expenses,
                'profit'       => $revenue - $refunds - $expenses,
                'reservations' => (int) ($reservationsByMonth[$m] ?? 0),
            ];
        }

        $yearTotals = [
            'revenue'      => array_sum(array_column($months, 'revenue')),
            'refunds'      => array_sum(array_column($months, 'refunds')),
            'net_revenue'  => array_sum(array_column($months, 'net_revenue')),
            'expenses'     => array_sum(array_column($months, 'expenses')),
            'profit'       => array_sum(array_column($months, 'profit')),
            'reservations' => array_sum(array_column($months, 'reservations')),
        ];

        // Available years (union of payment and expense years)
        $yearSql = $driver === 'pgsql' ? 'EXTRACT(YEAR FROM payment_date)::int AS y'
                                       : "CAST(strftime('%Y', payment_date) AS INTEGER) AS y";
        $yearSqlExp = $driver === 'pgsql' ? 'EXTRACT(YEAR FROM expense_date)::int AS y'
                                          : "CAST(strftime('%Y', expense_date) AS INTEGER) AS y";

        $availableYears = collect(
            DB::table('payments')->where('business_id', $business->id)->selectRaw($yearSql)
              ->union(DB::table('expenses')->where('business_id', $business->id)->selectRaw($yearSqlExp))
              ->get()
        )->pluck('y')->unique()->sort()->values();

        if (! $availableYears->contains($year)) {
            $availableYears->push($year);
        }

        return view('expenses.report', compact(
            'months', 'yearTotals', 'year', 'availableYears', 'byCategory', 'business'
        ));
    }
}
