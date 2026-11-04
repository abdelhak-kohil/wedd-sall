<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\VenueSpace;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user     = $request->user() ?? auth()->user();
        $business = $user?->business ?? Business::first();
        $bid      = $business?->id ?? 1;
        $today    = now()->toDateString();
        $month    = now()->month;
        $year     = now()->year;

        // ── Today ───────────────────────────────────────────────
        $todayReservations = Reservation::where('business_id', $bid)
            ->whereDate('event_date', $today)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED])
            ->with('space')
            ->orderBy('start_time')
            ->get();

        // ── Upcoming (next 30 days, confirmed/hold) ─────────────
        $upcomingReservations = Reservation::where('business_id', $bid)
            ->whereBetween('event_date', [now()->addDay()->toDateString(), now()->addDays(30)->toDateString()])
            ->whereIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_HOLD])
            ->with('space')
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        // ── Active temporary holds ──────────────────────────────
        $activeHolds = Reservation::where('business_id', $bid)
            ->where('status', Reservation::STATUS_HOLD)
            ->orderBy('hold_expires_at')
            ->get();

        $expiringToday = $activeHolds->filter(
            fn($r) => $r->hold_expires_at && $r->hold_expires_at->isToday()
        )->count();

        // ── Financial ───────────────────────────────────────────
        // Outstanding balances (all confirmed/hold with remaining > 0)
        $unpaidBalance = Reservation::where('business_id', $bid)
            ->whereIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_HOLD])
            ->where('remaining_balance', '>', 0)
            ->sum('remaining_balance');

        // Revenue collected this month
        $monthRevenue = Payment::where('business_id', $bid)
            ->where('payment_type', '!=', Payment::TYPE_REFUND)
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->sum('amount');

        // Expenses this month
        $monthExpenses = Expense::where('business_id', $bid)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $monthProfit = $monthRevenue - $monthExpenses;

        // ── Customers ───────────────────────────────────────────
        $totalCustomers = Customer::where('business_id', $bid)->count();
        $newCustomers   = Customer::where('business_id', $bid)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        // ── Recent activity (last 5 reservations created) ───────
        $recentReservations = Reservation::where('business_id', $bid)
            ->with(['space', 'customer'])
            ->latest()
            ->limit(6)
            ->get();

        // ── Occupancy this month ─────────────────────────────────
        // Days in month × number of spaces (rough metric)
        $spaceCount   = VenueSpace::where('business_id', $bid)->count() ?: 1;
        $daysInMonth  = now()->daysInMonth;
        $bookedSlots  = Reservation::where('business_id', $bid)
            ->whereMonth('event_date', $month)
            ->whereYear('event_date', $year)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED])
            ->count();
        $occupancyPct = min(100, round($bookedSlots / ($daysInMonth * $spaceCount) * 100));

        // ── Inventory alerts ─────────────────────────────────────
        $lowStockItems = \App\Models\InventoryItem::where('business_id', $bid)
            ->where('is_active', true)
            ->whereColumn('quantity_available', '<=', 'quantity_alert')
            ->orderBy('quantity_available')
            ->limit(4)
            ->get();

        $stats = [
            'today_count'        => $todayReservations->count(),
            'upcoming_count'     => $upcomingReservations->count(),
            'active_holds'       => $activeHolds->count(),
            'expiring_today'     => $expiringToday,
            'unpaid_balance'     => $unpaidBalance,
            'month_revenue'      => $monthRevenue,
            'month_expenses'     => $monthExpenses,
            'month_profit'       => $monthProfit,
            'total_customers'    => $totalCustomers,
            'new_customers'      => $newCustomers,
            'occupancy_pct'      => $occupancyPct,
            'booked_this_month'  => $bookedSlots,
            'low_stock_count'    => $lowStockItems->count(),
        ];

        return view('dashboard', compact(
            'user', 'business', 'stats',
            'todayReservations', 'upcomingReservations',
            'activeHolds', 'recentReservations', 'lowStockItems'
        ));
    }
}
