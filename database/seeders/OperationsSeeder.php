<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\EventTask;
use App\Models\EventTimeline;
use App\Models\EventVendor;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class OperationsSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first();
        if (!$business) {
            return;
        }

        $user = User::where('business_id', $business->id)->first();
        $userId = $user?->id;

        $reservations = Reservation::where('business_id', $business->id)->get();

        foreach ($reservations as $reservation) {
            // 1. Generate default checklist
            EventTask::generateDefaultChecklist($reservation, $userId);

            // 2. Generate default timeline
            EventTimeline::generateDefaultTimeline($reservation);

            // If it's today's event (RES-2026-0001), mark J-1 and some Day-of tasks as completed
            if ($reservation->reservation_number === 'RES-2026-0001') {
                EventTask::where('reservation_id', $reservation->id)
                    ->whereIn('timing_phase', ['J_MINUS_1', 'DAY_OF_SETUP'])
                    ->limit(6)
                    ->update([
                        'status'       => EventTask::STATUS_COMPLETED,
                        'completed_at' => now()->subHours(2),
                        'completed_by' => $userId,
                    ]);

                // Mark first timeline step as done
                EventTimeline::where('reservation_id', $reservation->id)
                    ->orderBy('sort_order')
                    ->first()
                    ?->update(['is_done' => true]);

                // Add Realistic External Vendors for RES-2026-0001
                $vendors = [
                    [
                        'category'       => EventVendor::CATEGORY_TRAITEUR,
                        'company_name'   => 'Traiteur Royal El Mordjene',
                        'contact_person' => 'Chef Mustapha',
                        'phone'          => '0550 44 33 22',
                        'arrival_time'   => '16:00',
                        'special_notes'  => 'Menu traditionnel (Chorba frik, Couscous royal, Tajine l\'ham l\'hehlou). 3 camions frigorifiques.',
                    ],
                    [
                        'category'       => EventVendor::CATEGORY_DJ,
                        'company_name'   => 'DJ Amine Sound & Light',
                        'contact_person' => 'Amine',
                        'phone'          => '0661 88 99 00',
                        'arrival_time'   => '17:00',
                        'special_notes'  => 'Prévoir branchement console régie salle + étincelles froides entrée.',
                    ],
                    [
                        'category'       => EventVendor::CATEGORY_PHOTO,
                        'company_name'   => 'Studio Lumière Mariage',
                        'contact_person' => 'Karim & Samira',
                        'phone'          => '0770 12 34 56',
                        'arrival_time'   => '18:00',
                        'special_notes'  => '2 caméramans + 1 photographe drone terrasse.',
                    ],
                    [
                        'category'       => EventVendor::CATEGORY_NEGAFA,
                        'company_name'   => 'Négafa La Princesse d\'Oran',
                        'contact_person' => 'Madame Meriem',
                        'phone'          => '0555 99 88 77',
                        'arrival_time'   => '17:30',
                        'special_notes'  => 'Loge mariée VIP requise avec fer à repasser vapeur et grands miroirs éclairés.',
                    ],
                    [
                        'category'       => EventVendor::CATEGORY_PATISSERIE,
                        'company_name'   => 'Pâtisserie Prestige Gourmet',
                        'contact_person' => 'Rachid',
                        'phone'          => '0670 55 44 33',
                        'arrival_time'   => '21:00',
                        'special_notes'  => 'Livraison pièce montée 5 étages. Stocker dans chambre froide négative.',
                    ],
                ];

                foreach ($vendors as $v) {
                    EventVendor::firstOrCreate(
                        [
                            'business_id'    => $business->id,
                            'reservation_id' => $reservation->id,
                            'company_name'   => $v['company_name'],
                        ],
                        array_merge($v, [
                            'business_id'    => $business->id,
                            'reservation_id' => $reservation->id,
                            'is_confirmed'   => true,
                        ])
                    );
                }
            }
        }
    }
}
