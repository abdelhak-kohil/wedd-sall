<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VenueSpace;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageQuotationAndContractTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Business $business;
    protected VenueSpace $space;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::first();
        $this->business = Business::first();
        $this->space = VenueSpace::first();
        $this->package = Package::first();
    }

    public function test_can_list_and_create_package(): void
    {
        $response = $this->actingAs($this->user)->get(route('packages.index'));
        $response->assertOk();
        $response->assertSee('Pack Essentiel');

        $this->actingAs($this->user)->post(route('packages.store'), [
            'name_fr' => 'Pack Fiançailles Doré',
            'name_ar' => 'باقة الخطوبة الذهبية',
            'base_price' => 150000,
            'price_per_guest' => 500,
            'included_items_text' => "Trône floral\nDJ Animateur\nThé et café",
        ]);

        $this->assertDatabaseHas('packages', [
            'name_fr' => 'Pack Fiançailles Doré',
            'base_price' => 150000,
        ]);
    }

    public function test_can_create_and_view_quotation(): void
    {
        $date = now()->addDays(25)->format('Y-m-d');

        $response = $this->actingAs($this->user)->post(route('quotations.store'), [
            'customer_name' => 'Kamel Chaabane',
            'customer_phone' => '0550998877',
            'groom_name' => 'Kamel',
            'bride_name' => 'Sarah',
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:30',
            'expected_guests' => 250,
            'package_id' => $this->package->id,
            'total_price' => 280000,
            'deposit_required' => 70000,
            'valid_days' => 15,
            'notes' => 'Devis comprenant salle et sonorisation',
        ]);

        $quotation = Quotation::where('customer_phone', '0550998877')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals('SENT', $quotation->status);
        $response->assertRedirect(route('quotations.show', $quotation));

        // Test print layout
        $printResponse = $this->actingAs($this->user)->get(route('quotations.print', $quotation));
        $printResponse->assertOk();
        $printResponse->assertSee($quotation->quote_number);
        $printResponse->assertSee('280 000,00 DA');
    }

    public function test_can_convert_quotation_to_reservation(): void
    {
        $date = now()->addDays(35)->format('Y-m-d');

        $quotation = Quotation::create([
            'business_id' => $this->business->id,
            'quote_number' => 'DEV-TEST-0001',
            'customer_name' => 'Hichem Bensaad',
            'customer_phone' => '0661223344',
            'groom_name' => 'Hichem',
            'bride_name' => 'Leila',
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:30',
            'expected_guests' => 300,
            'package_id' => $this->package->id,
            'total_price' => 320000,
            'deposit_required' => 80000,
            'status' => Quotation::STATUS_SENT,
            'valid_until' => now()->addDays(15),
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('quotations.convert', $quotation));

        $quotation->refresh();
        $this->assertEquals(Quotation::STATUS_ACCEPTED, $quotation->status);
        $this->assertNotNull($quotation->converted_reservation_id);

        $reservation = Reservation::find($quotation->converted_reservation_id);
        $this->assertEquals('Hichem Bensaad', $reservation->customer_name);
        $this->assertEquals(320000, (float) $reservation->total_price);
        $response->assertRedirect(route('reservations.show', $reservation));
    }

    public function test_can_render_official_algerian_contract(): void
    {
        $date = now()->addDays(40)->format('Y-m-d');

        $reservation = Reservation::create([
            'business_id' => $this->business->id,
            'reservation_number' => 'RES-CONTRACT-001',
            'venue_space_id' => $this->space->id,
            'customer_name' => 'Yacine Merad',
            'customer_phone' => '0770991122',
            'groom_name' => 'Yacine',
            'bride_name' => 'Meriem',
            'event_type' => 'Mariage',
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:30',
            'expected_guests' => 350,
            'status' => Reservation::STATUS_CONFIRMED,
            'payment_status' => 'UNPAID',
            'total_price' => 300000,
            'deposit_required' => 60000,
            'deposit_paid' => 60000,
            'total_paid' => 60000,
            'remaining_balance' => 240000,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('reservations.contract', $reservation));
        $response->assertOk();
        $response->assertSee('CONTRAT DE LOCATION DE SALLE DES FÊTES');
        $response->assertSee('عقد كراء قاعة حفلات ومناسبات');
        $response->assertSee('RES-CONTRACT-001');
        $response->assertSee('Yacine Merad');
        $response->assertSee('ARTICLE 5 : DÉSISTEMENT ET ANNULATION');
    }
}
