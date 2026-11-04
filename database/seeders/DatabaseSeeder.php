<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueSpace;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Algerian Wedding Venue Business
        $business = Business::firstOrCreate(
            ['slug' => 'palais-el-bahia'],
            [
                'name_fr' => 'Palais El Bahia',
                'name_ar' => 'قصر الباهية للأفراح والمناسبات',
                'phone' => '0550 12 34 56',
                'whatsapp' => '0550 12 34 56',
                'email' => 'contact@palaiselbahia.dz',
                'wilaya' => 'Alger',
                'commune' => 'Alger Centre',
                'address_line' => '14 Boulevard des Martyrs, Alger',
                'nif' => '099816001234567',
                'nis' => '1600123456789',
                'rc' => '16/00-1234567B22',
                'currency' => 'DZD',
                'timezone' => 'Africa/Algiers',
                'is_active' => true,
                'settings' => [
                    'contract_notice_days' => 15,
                    'cleaning_buffer_minutes' => 60,
                    'default_deposit_percentage' => 30,
                ],
            ]
        );

        // 2. Create the 5 Canonical Roles Users
        $users = [
            [
                'name' => 'Kamel Benali',
                'email' => 'owner@palaiselbahia.dz',
                'phone' => '0550 11 22 33',
                'role' => User::ROLE_OWNER,
                'preferred_language' => 'fr',
            ],
            [
                'name' => 'Sara Mansouri',
                'email' => 'manager@palaiselbahia.dz',
                'phone' => '0661 44 55 66',
                'role' => User::ROLE_MANAGER,
                'preferred_language' => 'fr',
            ],
            [
                'name' => 'Yasmine Belkacem',
                'email' => 'reception@palaiselbahia.dz',
                'phone' => '0770 77 88 99',
                'role' => User::ROLE_RECEPTIONIST,
                'preferred_language' => 'ar',
            ],
            [
                'name' => 'Ahmed Touati',
                'email' => 'compta@palaiselbahia.dz',
                'phone' => '0560 33 44 55',
                'role' => User::ROLE_ACCOUNTANT,
                'preferred_language' => 'fr',
            ],
            [
                'name' => 'Amine Khelifi',
                'email' => 'events@palaiselbahia.dz',
                'phone' => '0671 22 33 44',
                'role' => User::ROLE_COORDINATOR,
                'preferred_language' => 'ar',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'business_id' => $business->id,
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'role' => $userData['role'],
                    'password' => Hash::make('password'),
                    'preferred_language' => $userData['preferred_language'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Create Venue Establishment
        $venue = Venue::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Palais El Bahia - Alger'],
            [
                'name_ar' => 'قصر الباهية - الجزائر العاصمة',
                'address' => '14 Boulevard des Martyrs, Alger',
                'is_active' => true,
            ]
        );

        // 4. Create Venue Spaces (Multiple halls)
        $salleRoyale = VenueSpace::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Salle Royale'],
            [
                'venue_id' => $venue->id,
                'name_ar' => 'القاعة الملكية',
                'capacity_min' => 150,
                'capacity_max' => 450,
                'base_price' => 250000.00,
                'is_active' => true,
            ]
        );

        $salleAndalouse = VenueSpace::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Salle Andalouse'],
            [
                'venue_id' => $venue->id,
                'name_ar' => 'القاعة الأندلسية',
                'capacity_min' => 80,
                'capacity_max' => 250,
                'base_price' => 180000.00,
                'is_active' => true,
            ]
        );

        $terrasse = VenueSpace::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Terrasse Panoramique'],
            [
                'venue_id' => $venue->id,
                'name_ar' => 'الشرفة البانورامية',
                'capacity_min' => 50,
                'capacity_max' => 150,
                'base_price' => 120000.00,
                'is_active' => true,
            ]
        );

        // 5. Configurable Time Slots
        $slotApresMidi = TimeSlot::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Après-midi'],
            [
                'name_ar' => 'فترة الظهيرة والمساء',
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'is_overnight' => false,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $slotSoiree = TimeSlot::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Soirée'],
            [
                'name_ar' => 'سهرة العرس',
                'start_time' => '19:00:00',
                'end_time' => '02:00:00',
                'is_overnight' => true,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $slotJournee = TimeSlot::firstOrCreate(
            ['business_id' => $business->id, 'name_fr' => 'Journée Entière'],
            [
                'name_ar' => 'يوم كامل',
                'start_time' => '10:00:00',
                'end_time' => '02:00:00',
                'is_overnight' => true,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // 6. Realistic Sample Reservations
        // A) Confirmed Wedding today in Salle Royale
        Reservation::firstOrCreate(
            ['reservation_number' => 'RES-2026-0001'],
            [
                'business_id' => $business->id,
                'venue_space_id' => $salleRoyale->id,
                'time_slot_id' => $slotSoiree->id,
                'customer_name' => 'Ahmed Bouzid',
                'customer_phone' => '0550 99 88 77',
                'bride_name' => 'Sarah',
                'groom_name' => 'Ahmed',
                'event_type' => 'MARIAGE',
                'event_date' => Carbon::today(),
                'start_time' => '19:00:00',
                'end_time' => '02:00:00',
                'expected_guests' => 320,
                'status' => Reservation::STATUS_CONFIRMED,
                'payment_status' => Reservation::PAYMENT_PARTIALLY_PAID,
                'total_price' => 450000.00,
                'deposit_required' => 100000.00,
                'deposit_paid' => 100000.00,
                'total_paid' => 270000.00,
                'remaining_balance' => 180000.00,
                'internal_notes' => 'Solde restant de 180 000 DA à régler ce soir avant l’entrée des mariés.',
            ]
        );

        // B) Temporary Hold tomorrow in Salle Andalouse
        Reservation::firstOrCreate(
            ['reservation_number' => 'RES-2026-0002'],
            [
                'business_id' => $business->id,
                'venue_space_id' => $salleAndalouse->id,
                'time_slot_id' => $slotSoiree->id,
                'customer_name' => 'Karim Meziani',
                'customer_phone' => '0661 12 34 56',
                'bride_name' => 'Amel',
                'groom_name' => 'Karim',
                'event_type' => 'MARIAGE',
                'event_date' => Carbon::tomorrow(),
                'start_time' => '19:00:00',
                'end_time' => '02:00:00',
                'expected_guests' => 220,
                'status' => Reservation::STATUS_HOLD,
                'payment_status' => Reservation::PAYMENT_UNPAID,
                'hold_expires_at' => Carbon::now()->addHours(18),
                'total_price' => 380000.00,
                'deposit_required' => 100000.00,
                'deposit_paid' => 0.00,
                'total_paid' => 0.00,
                'remaining_balance' => 380000.00,
                'internal_notes' => 'Option posée par le père du marié. En attente de versement de l’acompte.',
            ]
        );

        // C) Engagement this weekend in Salle Andalouse (Afternoon slot)
        $nextSaturday = Carbon::today()->isSaturday() ? Carbon::today() : Carbon::parse('next saturday');
        Reservation::firstOrCreate(
            ['reservation_number' => 'RES-2026-0003'],
            [
                'business_id' => $business->id,
                'venue_space_id' => $salleAndalouse->id,
                'time_slot_id' => $slotApresMidi->id,
                'customer_name' => 'Samir Hadj',
                'customer_phone' => '0770 45 67 89',
                'bride_name' => 'Lina',
                'groom_name' => 'Samir',
                'event_type' => 'FIANCAILLES',
                'event_date' => $nextSaturday,
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
                'expected_guests' => 150,
                'status' => Reservation::STATUS_CONFIRMED,
                'payment_status' => Reservation::PAYMENT_DEPOSIT_PAID,
                'total_price' => 200000.00,
                'deposit_required' => 50000.00,
                'deposit_paid' => 50000.00,
                'total_paid' => 50000.00,
                'remaining_balance' => 150000.00,
                'internal_notes' => 'Fiançailles après-midi. Salle libre pour la soirée.',
            ]
        );

        $this->call(WilayaSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(OperationsSeeder::class);
    }
}
