<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\VenueSpace;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Check if a space is available for the given date and time window.
     * Overlap formula: (start_time < new_end) AND (end_time > new_start)
     */
    public function checkSpaceAvailability(
        VenueSpace|int $space,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeReservationId = null
    ): array {
        $spaceId = $space instanceof VenueSpace ? $space->id : $space;

        $query = Reservation::where('venue_space_id', $spaceId)
            ->whereDate('event_date', $date)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_ARCHIVED]);

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        // Interval overlap condition:
        // (A_start < B_end) AND (A_end > B_start)
        $conflicts = $query->get()->filter(function (Reservation $res) use ($startTime, $endTime) {
            return $this->intervalsOverlap($res->start_time, $res->end_time, $startTime, $endTime);
        });

        if ($conflicts->isEmpty()) {
            return [
                'available' => true,
                'status' => 'AVAILABLE',
                'conflict' => null,
                'message' => 'Créneau disponible.',
            ];
        }

        /** @var Reservation $firstConflict */
        $firstConflict = $conflicts->first();

        if ($firstConflict->status === Reservation::STATUS_HOLD) {
            if ($firstConflict->isHoldExpired()) {
                return [
                    'available' => true,
                    'status' => 'EXPIRED_HOLD',
                    'conflict' => $firstConflict,
                    'message' => "Option temporaire expirée pour {$firstConflict->customer_name}. Peut être reprise.",
                ];
            }

            return [
                'available' => false,
                'status' => 'HOLD',
                'conflict' => $firstConflict,
                'message' => "Option temporaire active pour {$firstConflict->customer_name} (expire dans {$firstConflict->hold_hours_remaining}h).",
            ];
        }

        return [
            'available' => false,
            'status' => 'BOOKED',
            'conflict' => $firstConflict,
            'message' => "Créneau déjà réservé par {$firstConflict->customer_name} ({$firstConflict->time_range}).",
        ];
    }

    /**
     * Calculate whether two time ranges overlap.
     * Formats supported: 'HH:MM' or 'HH:MM:SS'. Handles overnight slots (e.g. 19:00 -> 02:00).
     */
    public function intervalsOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        $minutesA1 = $this->timeToMinutes($startA);
        $minutesA2 = $this->timeToMinutes($endA);
        if ($minutesA2 <= $minutesA1) {
            $minutesA2 += 1440; // overnight (+24h)
        }

        $minutesB1 = $this->timeToMinutes($startB);
        $minutesB2 = $this->timeToMinutes($endB);
        if ($minutesB2 <= $minutesB1) {
            $minutesB2 += 1440; // overnight (+24h)
        }

        return max($minutesA1, $minutesB1) < min($minutesA2, $minutesB2);
    }

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        return ((int) $parts[0]) * 60 + ((int) ($parts[1] ?? 0));
    }

    /**
     * Returns full availability matrix for all spaces and active time slots on a given date.
     */
    public function getDailyAvailabilityMatrix(Business $business, string $date): array
    {
        $spaces = VenueSpace::where('business_id', $business->id)
            ->where('is_active', true)
            ->with('venue')
            ->orderBy('capacity_max', 'desc')
            ->get();

        $slots = TimeSlot::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $reservations = Reservation::where('business_id', $business->id)
            ->whereDate('event_date', $date)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_ARCHIVED])
            ->get();

        $matrix = [];

        foreach ($spaces as $space) {
            $spaceSlots = [];
            $spaceReservations = $reservations->where('venue_space_id', $space->id);

            foreach ($slots as $slot) {
                $availability = $this->checkSpaceAvailability(
                    $space,
                    $date,
                    $slot->start_time,
                    $slot->end_time
                );

                $spaceSlots[] = [
                    'slot' => $slot,
                    'status' => $availability['status'],
                    'available' => $availability['available'],
                    'conflict' => $availability['conflict'],
                    'message' => $availability['message'],
                ];
            }

            $matrix[] = [
                'space' => $space,
                'slots' => $spaceSlots,
                'active_reservations_count' => $spaceReservations->count(),
            ];
        }

        return $matrix;
    }

    /**
     * Get all reservations for month calendar view.
     */
    public function getMonthCalendarEvents(Business $business, int $year, int $month, ?int $spaceId = null): Collection
    {
        $query = Reservation::where('business_id', $business->id)
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->where('status', '!=', 'CANCELLED')
            ->with(['space', 'timeSlot'])
            ->orderBy('event_date')
            ->orderBy('start_time');

        if ($spaceId) {
            $query->where('venue_space_id', $spaceId);
        }

        return $query->get();
    }

    /**
     * Check if a proposed visit time conflicts with any active reservation.
     * A visit is typically 45 minutes.
     */
    public function checkVisitConflict(
        VenueSpace|int $space,
        string $date,
        string $visitTime,
        int $durationMinutes = 45
    ): ?Reservation {
        $spaceId = $space instanceof VenueSpace ? $space->id : $space;

        $visitMinutesStart = $this->timeToMinutes($visitTime);
        $visitMinutesEnd = $visitMinutesStart + $durationMinutes;

        $reservations = Reservation::where('venue_space_id', $spaceId)
            ->whereDate('event_date', $date)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_ARCHIVED])
            ->get();

        foreach ($reservations as $res) {
            $resStart = $this->timeToMinutes($res->start_time);
            $resEnd = $this->timeToMinutes($res->end_time);
            if ($resEnd <= $resStart) {
                $resEnd += 1440; // overnight event
            }

            // Conflict condition: intervals overlap
            if (max($visitMinutesStart, $resStart) < min($visitMinutesEnd, $resEnd)) {
                return $res;
            }
        }

        return null;
    }

    /**
     * Get active reservations for a space on a specific date.
     */
    public function getSpaceEventsOnDate(VenueSpace|int $space, string $date): Collection
    {
        $spaceId = $space instanceof VenueSpace ? $space->id : $space;

        return Reservation::where('venue_space_id', $spaceId)
            ->whereDate('event_date', $date)
            ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_ARCHIVED])
            ->orderBy('start_time')
            ->get();
    }
}
