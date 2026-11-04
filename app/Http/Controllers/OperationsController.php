<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\EventTask;
use App\Models\EventTimeline;
use App\Models\EventVendor;
use App\Models\Reservation;
use App\Models\VenueSpace;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationsController extends Controller
{
    private function getBusiness(Request $request): Business
    {
        return $request->user()?->business ?? Business::first();
    }

    /**
     * Operational Hub: overview of upcoming events and readiness status
     */
    public function index(Request $request): View
    {
        $business = $this->getBusiness($request);
        $bid      = $business->id;
        $range    = $request->input('range', '7days'); // today, 7days, 30days, all
        $spaceId  = $request->input('space_id');

        $query = Reservation::where('business_id', $bid)
            ->whereIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_HOLD])
            ->with(['space', 'tasks', 'vendors', 'timelines'])
            ->orderBy('event_date')
            ->orderBy('start_time');

        $today = Carbon::today();

        if ($range === 'today') {
            $query->whereDate('event_date', $today);
        } elseif ($range === '7days') {
            $query->whereBetween('event_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()]);
        } elseif ($range === '30days') {
            $query->whereBetween('event_date', [$today->toDateString(), $today->copy()->addDays(30)->toDateString()]);
        } else {
            $query->where('event_date', '>=', $today->toDateString());
        }

        if ($spaceId) {
            $query->where('venue_space_id', $spaceId);
        }

        $events = $query->get();

        // Global Operational KPIs
        $next48hCount = Reservation::where('business_id', $bid)
            ->whereIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_HOLD])
            ->whereBetween('event_date', [$today->toDateString(), $today->copy()->addDays(2)->toDateString()])
            ->count();

        $criticalTasksPending = EventTask::where('business_id', $bid)
            ->where('is_critical', true)
            ->where('status', '!=', EventTask::STATUS_COMPLETED)
            ->whereHas('reservation', function ($q) use ($today) {
                $q->whereBetween('event_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()]);
            })
            ->count();

        $spaces = VenueSpace::where('business_id', $bid)->get();

        return view('operations.index', compact(
            'events', 'business', 'spaces', 'range', 'spaceId',
            'next48hCount', 'criticalTasksPending'
        ));
    }

    /**
     * Event Operational Command Center
     */
    public function show(Request $request, Reservation $reservation): View
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $reservation->load(['space', 'customer', 'tasks.completedByUser', 'vendors', 'timelines']);

        $tasks = $reservation->tasks;
        $totalTasks = $tasks->count();
        $doneTasks = $tasks->where('status', EventTask::STATUS_COMPLETED)->count();
        $progressPct = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;

        // Group tasks by timing phase
        $tasksByPhase = $tasks->groupBy('timing_phase');

        return view('operations.show', compact(
            'reservation', 'business', 'tasks', 'totalTasks', 'doneTasks',
            'progressPct', 'tasksByPhase'
        ));
    }

    /**
     * Auto-generate default checklist for a reservation
     */
    public function generateChecklist(Request $request, Reservation $reservation): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $count = EventTask::generateDefaultChecklist($reservation, $request->user()?->id);

        return back()->with('success', "Checklist standard générée avec succès ({$count} tâches créées).");
    }

    /**
     * Auto-generate default day-of timeline
     */
    public function generateTimeline(Request $request, Reservation $reservation): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $count = EventTimeline::generateDefaultTimeline($reservation);

        return back()->with('success', "Déroulement type Jour J généré avec succès ({$count} étapes créées).");
    }

    /**
     * Toggle a task's completed status
     */
    public function toggleTask(Request $request, EventTask $task)
    {
        $business = $this->getBusiness($request);
        abort_if($task->business_id !== $business->id, 403);

        if ($task->status === EventTask::STATUS_COMPLETED) {
            $task->update([
                'status'       => EventTask::STATUS_PENDING,
                'completed_at' => null,
                'completed_by' => null,
            ]);
        } else {
            $task->update([
                'status'       => EventTask::STATUS_COMPLETED,
                'completed_at' => now(),
                'completed_by' => $request->user()?->id,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status'       => $task->status,
                'completed'    => $task->status === EventTask::STATUS_COMPLETED,
                'completed_at' => $task->completed_at?->format('H:i'),
            ]);
        }

        return back()->with('success', 'Statut de la tâche mis à jour.');
    }

    /**
     * Add a custom task to a reservation
     */
    public function storeTask(Request $request, Reservation $reservation): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|in:' . implode(',', array_keys(EventTask::CATEGORIES)),
            'timing_phase' => 'required|string|in:' . implode(',', array_keys(EventTask::TIMING_PHASES)),
            'is_critical'  => 'nullable|boolean',
            'notes'        => 'nullable|string|max:500',
        ]);

        EventTask::create([
            'business_id'    => $business->id,
            'reservation_id' => $reservation->id,
            'title'          => $data['title'],
            'category'       => $data['category'],
            'timing_phase'   => $data['timing_phase'],
            'status'         => EventTask::STATUS_PENDING,
            'is_critical'    => (bool) ($data['is_critical'] ?? false),
            'notes'          => $data['notes'] ?? null,
            'assigned_to'    => $request->user()?->id,
            'sort_order'     => $reservation->tasks()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Tâche opérationnelle ajoutée.');
    }

    /**
     * Remove a task
     */
    public function destroyTask(Request $request, EventTask $task): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($task->business_id !== $business->id, 403);

        $task->delete();
        return back()->with('success', 'Tâche supprimée.');
    }

    /**
     * Add an external vendor to an event
     */
    public function storeVendor(Request $request, Reservation $reservation): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $data = $request->validate([
            'category'       => 'required|string|in:' . implode(',', array_keys(EventVendor::CATEGORIES)),
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'required|string|max:50',
            'arrival_time'   => 'nullable|string|max:10',
            'special_notes'  => 'nullable|string|max:500',
        ]);

        EventVendor::create([
            'business_id'    => $business->id,
            'reservation_id' => $reservation->id,
            'category'       => $data['category'],
            'company_name'   => $data['company_name'],
            'contact_person' => $data['contact_person'] ?? null,
            'phone'          => $data['phone'],
            'arrival_time'   => $data['arrival_time'] ?? null,
            'special_notes'  => $data['special_notes'] ?? null,
            'is_confirmed'   => true,
        ]);

        return back()->with('success', 'Prestataire externe enregistré.');
    }

    /**
     * Remove a vendor
     */
    public function destroyVendor(Request $request, EventVendor $vendor): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($vendor->business_id !== $business->id, 403);

        $vendor->delete();
        return back()->with('success', 'Prestataire retiré.');
    }

    /**
     * Add a timeline milestone
     */
    public function storeTimeline(Request $request, Reservation $reservation): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $data = $request->validate([
            'time'             => 'required|string|max:10',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:500',
            'person_in_charge' => 'nullable|string|max:255',
        ]);

        EventTimeline::create([
            'business_id'      => $business->id,
            'reservation_id'   => $reservation->id,
            'time'             => $data['time'],
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'person_in_charge' => $data['person_in_charge'] ?? null,
            'is_done'          => false,
            'sort_order'       => $reservation->timelines()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Étape chronologique ajoutée.');
    }

    /**
     * Toggle timeline milestone
     */
    public function toggleTimeline(Request $request, EventTimeline $timeline)
    {
        $business = $this->getBusiness($request);
        abort_if($timeline->business_id !== $business->id, 403);

        $timeline->update(['is_done' => !$timeline->is_done]);

        if ($request->wantsJson()) {
            return response()->json(['is_done' => $timeline->is_done]);
        }

        return back()->with('success', 'Étape mise à jour.');
    }

    /**
     * Remove a timeline milestone
     */
    public function destroyTimeline(Request $request, EventTimeline $timeline): RedirectResponse
    {
        $business = $this->getBusiness($request);
        abort_if($timeline->business_id !== $business->id, 403);

        $timeline->delete();
        return back()->with('success', 'Étape supprimée.');
    }

    /**
     * Printable A4 "Feuille de Route Opérationnelle Jour J"
     */
    public function printRoadmap(Request $request, Reservation $reservation): View
    {
        $business = $this->getBusiness($request);
        abort_if($reservation->business_id !== $business->id, 403);

        $reservation->load(['space', 'customer', 'tasks', 'vendors', 'timelines']);

        return view('operations.print', compact('reservation', 'business'));
    }
}
