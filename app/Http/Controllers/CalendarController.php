<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\VenueSpace;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $spaceId = $request->filled('space_id') ? (int) $request->space_id : null;

        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        $spaces = VenueSpace::where('business_id', $business->id)->where('is_active', true)->get();

        $reservations = $this->availabilityService->getMonthCalendarEvents(
            $business,
            $year,
            $month,
            $spaceId
        );

        // Group reservations by 'Y-m-d'
        $reservationsByDate = $reservations->groupBy(function ($item) {
            return $item->event_date->format('Y-m-d');
        });

        // Build month days matrix (Monday to Sunday)
        $startOfCalendar = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarDays = [];
        $dayIterator = $startOfCalendar->copy();

        while ($dayIterator->lte($endOfCalendar)) {
            $dateKey = $dayIterator->format('Y-m-d');
            $calendarDays[] = [
                'date' => $dayIterator->copy(),
                'is_current_month' => $dayIterator->month === $month,
                'is_today' => $dayIterator->isToday(),
                'events' => $reservationsByDate->get($dateKey, collect()),
            ];
            $dayIterator->addDay();
        }

        return view('calendar.index', compact(
            'business',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'spaces',
            'spaceId',
            'calendarDays',
            'reservations'
        ));
    }
}
