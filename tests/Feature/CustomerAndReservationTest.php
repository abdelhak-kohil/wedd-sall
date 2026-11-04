<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VenueSpace;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAndReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Business $business;
    protected VenueSpace $space;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::first();
        $this->business = Business::first();
        $this->space = VenueSpace::first();
    }

    public function test_can_create_customer(): void
    {
        $response = $this->actingAs($this->user)->post(route('customers.store'), [
            'full_name' => 'Karim Benali',
            'phone' => '0555123456',
            'wilaya_id' => 16,
            'source' => 'RECOMMENDATION',
            'bride_name' => 'Amina',
            'groom_name' => 'Karim',
            'preferred_language' => 'fr',
        ]);

        $this->assertDatabaseHas('customers', [
            'full_name' => 'Karim Benali',
            'phone' => '0555123456',
            'wilaya_id' => 16,
        ]);

        $customer = Customer::where('phone', '0555123456')->first();
        $response->assertRedirect(route('customers.show', $customer->id));
        $this->assertStringContainsString('wa.me/213555123456', $customer->whatsappLink());
    }

    public function test_can_create_quick_reservation_and_enforces_availability(): void
    {
        $date = now()->addDays(20)->format('Y-m-d');

        // 1. Create first reservation
        $response = $this->actingAs($this->user)->post(route('reservations.store'), [
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:30',
            'event_type' => 'Mariage',
            'expected_guests' => 300,
            'customer_name' => 'Amine Touati',
            'customer_phone' => '0666112233',
            'groom_name' => 'Amine',
            'bride_name' => 'Nora',
            'status' => 'CONFIRMED',
            'total_price' => 350000,
            'deposit_required' => 70000,
            'create_customer' => 1,
            'wilaya_id' => 16,
        ]);

        $this->assertDatabaseHas('reservations', [
            'venue_space_id' => $this->space->id,
            'customer_name' => 'Amine Touati',
            'status' => 'CONFIRMED',
        ]);

        // 2. Attempt duplicate overlapping booking on same date and space -> rejected
        $overlapResponse = $this->actingAs($this->user)->post(route('reservations.store'), [
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '18:00',
            'end_time' => '22:00',
            'event_type' => 'Fiançailles',
            'expected_guests' => 150,
            'customer_name' => 'Rachid Hadj',
            'customer_phone' => '0777334455',
            'status' => 'CONFIRMED',
            'total_price' => 200000,
            'deposit_required' => 50000,
        ]);

        $overlapResponse->assertSessionHasErrors('venue_space_id');
    }

    public function test_can_update_reservation_status_and_cancel(): void
    {
        $date = now()->addDays(30)->format('Y-m-d');

        $reservation = Reservation::create([
            'business_id' => $this->business->id,
            'reservation_number' => 'RES-TEST-001',
            'venue_space_id' => $this->space->id,
            'customer_name' => 'Sofiane Larbi',
            'customer_phone' => '0555998877',
            'event_type' => 'Mariage',
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:30',
            'expected_guests' => 250,
            'status' => 'TEMPORARY_HOLD',
            'payment_status' => 'UNPAID',
            'total_price' => 280000,
            'deposit_required' => 50000,
            'deposit_paid' => 0,
            'total_paid' => 0,
            'remaining_balance' => 280000,
            'created_by' => $this->user->id,
        ]);

        // Confirm
        $this->actingAs($this->user)->patch(route('reservations.status', $reservation), [
            'status' => 'CONFIRMED',
        ]);
        $this->assertEquals('CONFIRMED', $reservation->fresh()->status);

        // Cancel with reason
        $this->actingAs($this->user)->patch(route('reservations.status', $reservation), [
            'status' => 'CANCELLED',
            'cancellation_reason' => 'Client a changé de ville',
        ]);
        $this->assertEquals('CANCELLED', $reservation->fresh()->status);
        $this->assertEquals('Client a changé de ville', $reservation->fresh()->cancellation_reason);
    }

    public function test_expire_holds_command(): void
    {
        $pastHold = Reservation::create([
            'business_id' => $this->business->id,
            'reservation_number' => 'RES-EXPIRE-TEST',
            'venue_space_id' => $this->space->id,
            'customer_name' => 'Expired Hold Test',
            'customer_phone' => '0555001122',
            'event_type' => 'Mariage',
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '23:30',
            'expected_guests' => 200,
            'status' => Reservation::STATUS_HOLD,
            'payment_status' => 'UNPAID',
            'hold_expires_at' => now()->subHour(), // Expired 1 hour ago
            'total_price' => 250000,
            'deposit_required' => 50000,
            'deposit_paid' => 0,
            'total_paid' => 0,
            'remaining_balance' => 250000,
            'created_by' => $this->user->id,
        ]);

        $this->artisan('reservations:expire-holds')
            ->expectsOutputToContain('Expired 1 temporary hold(s).')
            ->assertExitCode(0);

        $this->assertEquals(Reservation::STATUS_EXPIRED, $pastHold->fresh()->status);
    }
}
