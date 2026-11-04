<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\VenueSpace;
use App\Models\Wilaya;
use App\Services\AvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    protected AvailabilityService $availability;

    public function __construct(AvailabilityService $availability)
    {
        $this->availability = $availability;
    }

    public function index(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();

        $query = Reservation::where('business_id', $business->id)
            ->with(['space', 'customer', 'timeSlot'])
            ->orderByDesc('event_date');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($q = $request->input('q')) {
            $query->where(function ($subq) use ($q) {
                $subq->where('customer_name', 'ilike', "%{$q}%")
                     ->orWhere('reservation_number', 'ilike', "%{$q}%")
                     ->orWhere('bride_name', 'ilike', "%{$q}%")
                     ->orWhere('groom_name', 'ilike', "%{$q}%")
                     ->orWhere('customer_phone', 'ilike', "%{$q}%");
            });
        }

        $reservations = $query->paginate(20)->withQueryString();

        return view('reservations.index', compact('reservations', 'business'));
    }

    public function create(Request $request): View
    {
        $business = $request->user()->business ?? Business::first();
        $spaces = VenueSpace::where('business_id', $business->id)->where('is_active', true)->get();
        $slots = TimeSlot::where('business_id', $business->id)->where('is_active', true)->orderBy('sort_order')->get();
        $wilayas = Wilaya::orderBy('name_fr')->get();

        // Pre-fill from availability screen click
        $preSelectedSpace = $request->input('space_id');
        $preSelectedDate = $request->input('date');
        $preSelectedSlot = $request->input('slot_id');

        // Customer phone search pre-fill
        $foundCustomer = null;
        if ($searchPhone = $request->input('phone')) {
            $foundCustomer = Customer::where('business_id', $business->id)
                ->where('phone', $searchPhone)->first();
        }

        return view('reservations.create', compact(
            'spaces', 'slots', 'wilayas', 'business',
            'preSelectedSpace', 'preSelectedDate', 'preSelectedSlot', 'foundCustomer'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'venue_space_id' => 'required|exists:venue_spaces,id',
            'time_slot_id' => 'nullable|exists:time_slots,id',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'event_type' => 'required|string|max:50',
            'expected_guests' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:30',
            'bride_name' => 'nullable|string|max:120',
            'groom_name' => 'nullable|string|max:120',
            'status' => 'required|in:TEMPORARY_HOLD,CONFIRMED',
            'total_price' => 'required|numeric|min:0',
            'deposit_required' => 'required|numeric|min:0',
            'hold_hours' => 'nullable|integer|min:1|max:168',
            'internal_notes' => 'nullable|string',
            // Optional inline customer creation
            'create_customer' => 'nullable|boolean',
            'wilaya_id' => 'nullable|exists:wilayas,id',
        ]);

        $business = $request->user()->business ?? Business::first();
        $space = VenueSpace::findOrFail($validated['venue_space_id']);

        // 1. Availability check (enforced server-side)
        $check = $this->availability->checkSpaceAvailability(
            $space,
            $validated['event_date'],
            $validated['start_time'],
            $validated['end_time']
        );

        if (!$check['available'] && $check['status'] !== 'EXPIRED_HOLD') {
            return back()->withInput()
                ->withErrors(['venue_space_id' => '❌ ' . $check['message']]);
        }

        DB::transaction(function () use ($validated, $business, $request) {
            // 2. Create/link customer
            $customerId = null;
            if ($request->boolean('create_customer')) {
                $code = 'CLT-' . strtoupper(uniqid());
                $customer = Customer::create([
                    'business_id' => $business->id,
                    'customer_code' => $code,
                    'full_name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'wilaya_id' => $validated['wilaya_id'] ?? null,
                    'source' => 'WALK_IN',
                    'bride_name' => $validated['bride_name'] ?? null,
                    'groom_name' => $validated['groom_name'] ?? null,
                ]);
                $customerId = $customer->id;
            } elseif ($existingId = request()->input('customer_id')) {
                $customerId = $existingId;
            }

            // 3. Generate reservation number
            $count = Reservation::where('business_id', $business->id)->count() + 1;
            $resNumber = 'RES-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // 4. Build financial data
            $totalPrice = (float) $validated['total_price'];
            $depositRequired = (float) $validated['deposit_required'];
            $holdExpiresAt = null;

            if ($validated['status'] === Reservation::STATUS_HOLD) {
                $hours = (int) ($validated['hold_hours'] ?? 24);
                $holdExpiresAt = now()->addHours($hours);
            }

            $reservation = Reservation::create([
                'business_id' => $business->id,
                'reservation_number' => $resNumber,
                'customer_id' => $customerId,
                'venue_space_id' => $validated['venue_space_id'],
                'time_slot_id' => $validated['time_slot_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'bride_name' => $validated['bride_name'] ?? null,
                'groom_name' => $validated['groom_name'] ?? null,
                'event_type' => $validated['event_type'],
                'event_date' => $validated['event_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'expected_guests' => $validated['expected_guests'],
                'status' => $validated['status'],
                'payment_status' => Reservation::PAYMENT_UNPAID,
                'hold_expires_at' => $holdExpiresAt,
                'total_price' => $totalPrice,
                'deposit_required' => $depositRequired,
                'deposit_paid' => 0,
                'total_paid' => 0,
                'remaining_balance' => $totalPrice,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'created_by' => auth()->id(),
            ]);


            // 5. Audit log
            AuditLog::create([
                'business_id' => $business->id,
                'user_id' => auth()->id(),
                'action' => 'CREATE',
                'auditable_type' => Reservation::class,
                'auditable_id' => $reservation->id,
                'new_values' => ['number' => $reservation->reservation_number, 'status' => $reservation->status],
                'ip_address' => request()->ip(),
            ]);

            session(['last_created_reservation_id' => $reservation->id]);
        });

        $reservationId = session('last_created_reservation_id');

        return redirect()->route('reservations.show', $reservationId)
            ->with('success', 'Réservation créée avec succès.');
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['space.venue', 'timeSlot', 'customer.wilaya']);

        return view('reservations.show', compact('reservation'));
    }

    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:CONFIRMED,CANCELLED,COMPLETED,ARCHIVED',
            'cancellation_reason' => 'nullable|string',
        ]);

        $oldStatus = $reservation->status;

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === Reservation::STATUS_CANCELLED) {
            $updateData['cancelled_at'] = now();
            $updateData['cancelled_by'] = auth()->id();
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? null;
        }

        $reservation->update($updateData);

        AuditLog::create([
            'business_id' => $reservation->business_id,
            'user_id' => auth()->id(),
            'action' => 'STATUS_CHANGE',
            'auditable_type' => Reservation::class,
            'auditable_id' => $reservation->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $validated['status']],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Statut mis à jour: ' . $validated['status']);
    }
}
