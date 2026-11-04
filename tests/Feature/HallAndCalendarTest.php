<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\VenueSpace;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HallAndCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_overlapping_time_slots_are_rejected(): void
    {
        $service = app(AvailabilityService::class);
        $salleRoyale = VenueSpace::where('name_fr', 'Salle Royale')->first();

        // There is an existing booking today in Salle Royale from 19:00 to 02:00
        $result = $service->checkSpaceAvailability(
            $salleRoyale,
            Carbon::today()->toDateString(),
            '20:00:00',
            '23:00:00'
        );

        $this->assertFalse($result['available']);
        $this->assertEquals('BOOKED', $result['status']);
        $this->assertNotNull($result['conflict']);
    }

    public function test_non_overlapping_time_slots_on_same_hall_are_available(): void
    {
        $service = app(AvailabilityService::class);
        $salleRoyale = VenueSpace::where('name_fr', 'Salle Royale')->first();

        // 14:00 to 18:00 does not overlap with the 19:00 to 02:00 evening booking
        $result = $service->checkSpaceAvailability(
            $salleRoyale,
            Carbon::today()->toDateString(),
            '14:00:00',
            '18:00:00'
        );

        $this->assertTrue($result['available']);
        $this->assertEquals('AVAILABLE', $result['status']);
    }

    public function test_different_spaces_at_same_time_do_not_conflict(): void
    {
        $service = app(AvailabilityService::class);
        $terrasse = VenueSpace::where('name_fr', 'Terrasse Panoramique')->first();

        // Terrasse has no booking tonight, so 19:00 to 02:00 should be completely available
        $result = $service->checkSpaceAvailability(
            $terrasse,
            Carbon::today()->toDateString(),
            '19:00:00',
            '02:00:00'
        );

        $this->assertTrue($result['available']);
        $this->assertEquals('AVAILABLE', $result['status']);
    }

    public function test_temporary_hold_reported_with_ttl(): void
    {
        $service = app(AvailabilityService::class);
        $salleAndalouse = VenueSpace::where('name_fr', 'Salle Andalouse')->first();

        // Salle Andalouse has a hold tomorrow from 19:00 to 02:00
        $result = $service->checkSpaceAvailability(
            $salleAndalouse,
            Carbon::tomorrow()->toDateString(),
            '19:00:00',
            '02:00:00'
        );

        $this->assertFalse($result['available']);
        $this->assertEquals('HOLD', $result['status']);
        $this->assertGreaterThanOrEqual(1, $result['conflict']->hold_hours_remaining);
    }

    public function test_availability_screen_renders_successfully(): void
    {
        $user = User::where('email', 'reception@palaiselbahia.dz')->first();

        $response = $this->actingAs($user)->get('/availability');

        $response->assertStatus(200);
        $response->assertSee('Salle Royale');
        $response->assertSee('Salle Andalouse');
        $response->assertSee('Terrasse Panoramique');
    }

    public function test_calendar_screen_renders_with_events(): void
    {
        $user = User::where('email', 'owner@palaiselbahia.dz')->first();

        $response = $this->actingAs($user)->get('/calendar');

        $response->assertStatus(200);
        $response->assertSee('Salle Royale');
        $response->assertSee('Ahmed Bouzid');
    }

    public function test_spaces_configuration_page_accessible_by_owner(): void
    {
        $owner = User::where('email', 'owner@palaiselbahia.dz')->first();

        $response = $this->actingAs($owner)->get('/settings/spaces');

        $response->assertStatus(200);
        $response->assertSee('Salle Royale');
        $response->assertSee('Après-midi');
        $response->assertSee('Soirée');
    }
}
