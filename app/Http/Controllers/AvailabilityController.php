<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Reservation;
use App\Models\VenueSpace;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    protected AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        // Target Date (defaults to today)
        $dateStr = $request->input('date', Carbon::today()->toDateString());
        try {
            $selectedDate = Carbon::parse($dateStr);
        } catch (\Exception $e) {
            $selectedDate = Carbon::today();
        }

        // Quick date presets
        $presets = [
            'today' => Carbon::today(),
            'tomorrow' => Carbon::tomorrow(),
            'next_friday' => Carbon::today()->isFriday() ? Carbon::today() : Carbon::parse('next friday'),
            'next_saturday' => Carbon::today()->isSaturday() ? Carbon::today() : Carbon::parse('next saturday'),
        ];

        $matrix = $this->availabilityService->getDailyAvailabilityMatrix(
            $business,
            $selectedDate->toDateString()
        );

        return view('availability.index', compact(
            'business',
            'selectedDate',
            'presets',
            'matrix'
        ));
    }

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'space_id' => 'required|exists:venue_spaces,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'exclude_id' => 'nullable|integer',
        ]);

        $space = VenueSpace::findOrFail($request->space_id);

        $result = $this->availabilityService->checkSpaceAvailability(
            $space,
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->exclude_id ? (int) $request->exclude_id : null
        );

        return response()->json($result);
    }
}
