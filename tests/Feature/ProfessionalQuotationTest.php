<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VenueSpace;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfessionalQuotationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected VenueSpace $space;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->user = User::first();
        $this->space = VenueSpace::first();
        $this->package = Package::first();
    }

    public function test_quotations_index_page_loads_with_stats(): void
    {
        $response = $this->actingAs($this->user)->get(route('quotations.index'));
        $response->assertOk();
        $response->assertSee('Total Devis');
        $response->assertSee('Nouveau Devis Professionnel');
    }

    public function test_quotations_create_page_loads_with_form_elements(): void
    {
        $response = $this->actingAs($this->user)->get(route('quotations.create'));
        $response->assertOk();
        $response->assertSee('Nouveau Devis');
        $response->assertSee('Remise Commerciale');
        $response->assertSee('TVA');
        $response->assertSee('Détail Chiffré des Prestations');
        $response->assertSee('window.recalculateTotals', false);
        $response->assertSee('window.setDepositPercent', false);
        $response->assertSee('window.addPreset', false);
    }

    public function test_can_create_professional_quotation_with_line_items_discount_and_tax(): void
    {
        $date = now()->addDays(20)->format('Y-m-d');

        $payload = [
            'customer_name' => 'Walid Benmoussa',
            'customer_phone' => '0661234567',
            'groom_name' => 'Walid',
            'bride_name' => 'Amina',
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '17:00',
            'end_time' => '23:30',
            'expected_guests' => 200,
            'package_id' => $this->package->id,
            'items' => [
                [
                    'description' => 'Location Salle Prestige avec Loge',
                    'quantity' => 1,
                    'unit_price' => 200000,
                    'total' => 200000,
                ],
                [
                    'description' => 'Animation DJ & Sonorisation Pro',
                    'quantity' => 1,
                    'unit_price' => 35000,
                    'total' => 35000,
                ],
                [
                    'description' => 'Dîner Gastronomique Traiteur',
                    'quantity' => 200,
                    'unit_price' => 1000,
                    'total' => 200000,
                ],
            ],
            'subtotal' => 435000,
            'discount_type' => 'percent',
            'discount_value' => 10,
            'tax_rate' => 19,
            'total_price' => 465885, // (435000 - 43500) * 1.19 = 391500 * 1.19 = 465885
            'deposit_required' => 100000,
            'valid_days' => 15,
            'notes' => 'Décoration florale personnalisée incluse.',
            'payment_terms' => 'Acompte 100 000 DA à la signature.',
        ];

        $response = $this->actingAs($this->user)->post(route('quotations.store'), $payload);

        $quotation = Quotation::where('customer_phone', '0661234567')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals(435000, (float)$quotation->subtotal);
        $this->assertEquals('percent', $quotation->discount_type);
        $this->assertEquals(43500, (float)$quotation->discount_amount);
        $this->assertEquals(19, (float)$quotation->tax_rate);
        $this->assertEquals(74385, (float)$quotation->tax_amount);
        $this->assertEquals(465885, (float)$quotation->total_price);
        $this->assertEquals(100000, (float)$quotation->deposit_required);
        $this->assertEquals(365885, (float)$quotation->balance_due);
        $this->assertCount(3, $quotation->items);

        $response->assertRedirect(route('quotations.show', $quotation));

        // Test show view
        $showResponse = $this->actingAs($this->user)->get(route('quotations.show', $quotation));
        $showResponse->assertOk();
        $showResponse->assertSee('Walid Benmoussa');
        $showResponse->assertSee('Animation DJ & Sonorisation Pro');
        $showResponse->assertSee('Dîner Gastronomique Traiteur');

        // Test print view
        $printResponse = $this->actingAs($this->user)->get(route('quotations.print', $quotation));
        $printResponse->assertOk();
        $printResponse->assertSee($quotation->quote_number);
        $printResponse->assertSee('Bon pour accord');
    }

    public function test_can_edit_and_update_quotation(): void
    {
        $date = now()->addDays(20)->format('Y-m-d');
        $quotation = Quotation::create([
            'business_id' => $this->user->business_id ?? Business::first()->id,
            'quote_number' => 'DEV-2026-9999',
            'customer_name' => 'Anis Belhadj',
            'customer_phone' => '0555443322',
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:00',
            'expected_guests' => 150,
            'subtotal' => 200000,
            'discount_type' => 'none',
            'discount_value' => 0,
            'discount_amount' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total_price' => 200000,
            'deposit_required' => 50000,
            'status' => Quotation::STATUS_SENT,
            'valid_until' => now()->addDays(15),
            'items' => [
                ['description' => 'Location Salle Principale', 'quantity' => 1, 'unit_price' => 200000, 'total' => 200000],
            ],
        ]);

        // Test edit page loads
        $editResponse = $this->actingAs($this->user)->get(route('quotations.edit', $quotation));
        $editResponse->assertOk();
        $editResponse->assertSee('Anis Belhadj');
        $editResponse->assertSee('DEV-2026-9999');

        // Test update quotation
        $updatePayload = [
            'customer_name' => 'Anis Belhadj (Mis à jour)',
            'customer_phone' => '0555443322',
            'venue_space_id' => $this->space->id,
            'event_date' => $date,
            'start_time' => '16:00',
            'end_time' => '23:30',
            'expected_guests' => 180,
            'items' => [
                ['description' => 'Location Salle Principale', 'quantity' => 1, 'unit_price' => 220000, 'total' => 220000],
                ['description' => 'Animation DJ Pro', 'quantity' => 1, 'unit_price' => 30000, 'total' => 30000],
            ],
            'subtotal' => 250000,
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'tax_rate' => 0,
            'total_price' => 230000,
            'deposit_required' => 60000,
            'status' => Quotation::STATUS_SENT,
            'notes' => 'Mise à jour avec DJ',
        ];

        $response = $this->actingAs($this->user)->put(route('quotations.update', $quotation), $updatePayload);
        $response->assertRedirect(route('quotations.show', $quotation));

        $quotation->refresh();
        $this->assertEquals('Anis Belhadj (Mis à jour)', $quotation->customer_name);
        $this->assertEquals(250000, (float)$quotation->subtotal);
        $this->assertEquals(20000, (float)$quotation->discount_amount);
        $this->assertEquals(230000, (float)$quotation->total_price);
        $this->assertEquals(60000, (float)$quotation->deposit_required);
        $this->assertEquals(170000, (float)$quotation->balance_due);
        $this->assertCount(2, $quotation->items);
    }

    public function test_owner_and_manager_can_delete_quotation(): void
    {
        $this->user->update(['role' => User::ROLE_MANAGER]);

        $quotation = Quotation::create([
            'business_id' => $this->user->business_id ?? Business::first()->id,
            'quote_number' => 'DEV-2026-DEL1',
            'customer_name' => 'To Delete',
            'customer_phone' => '0555000111',
            'venue_space_id' => $this->space->id,
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '23:00',
            'expected_guests' => 100,
            'total_price' => 150000,
            'deposit_required' => 30000,
            'status' => Quotation::STATUS_DRAFT,
            'valid_until' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->user)->delete(route('quotations.destroy', $quotation));
        $response->assertRedirect(route('quotations.index'));
        $this->assertDatabaseMissing('quotations', ['id' => $quotation->id]);
    }

    public function test_non_manager_or_owner_cannot_delete_quotation(): void
    {
        $this->user->update(['role' => User::ROLE_RECEPTIONIST]);

        $quotation = Quotation::create([
            'business_id' => $this->user->business_id ?? Business::first()->id,
            'quote_number' => 'DEV-2026-NO-DEL',
            'customer_name' => 'Protected Quote',
            'customer_phone' => '0555000222',
            'venue_space_id' => $this->space->id,
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '23:00',
            'expected_guests' => 100,
            'total_price' => 150000,
            'deposit_required' => 30000,
            'status' => Quotation::STATUS_DRAFT,
            'valid_until' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->user)->delete(route('quotations.destroy', $quotation));
        $response->assertForbidden();
        $this->assertDatabaseHas('quotations', ['id' => $quotation->id]);
    }
}


