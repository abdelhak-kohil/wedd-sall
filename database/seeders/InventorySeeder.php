<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first();
        if (!$business) {
            return;
        }

        $user = User::where('business_id', $business->id)->first();
        $userId = $user?->id;

        $items = [
            // Mobilier
            [
                'name' => 'Tables rondes 8-10 personnes (Ø 180cm)',
                'category' => InventoryItem::CATEGORY_FURNITURE,
                'unit' => 'pièce',
                'quantity_total' => 45,
                'quantity_available' => 38,
                'quantity_alert' => 10,
                'description' => 'Tables pliantes haute résistance en polyéthylène pour banquets.',
                'location' => 'Hangar Réserve A - Rack 1',
                'unit_cost' => 18000.00,
            ],
            [
                'name' => 'Chaises Chiavari dorées avec galette',
                'category' => InventoryItem::CATEGORY_FURNITURE,
                'unit' => 'pièce',
                'quantity_total' => 420,
                'quantity_available' => 350,
                'quantity_alert' => 50,
                'description' => 'Chaises de mariage haut de gamme finition dorée avec coussin blanc amovible.',
                'location' => 'Hangar Réserve A - Racks 2 & 3',
                'unit_cost' => 4500.00,
            ],
            [
                'name' => 'Fauteuil trône des mariés (Kosha Royale)',
                'category' => InventoryItem::CATEGORY_FURNITURE,
                'unit' => 'pièce',
                'quantity_total' => 2,
                'quantity_available' => 2,
                'quantity_alert' => 1,
                'description' => 'Grand trône baroque capitonné blanc et or pour estrade des mariés.',
                'location' => 'Estrade / Stock Scène',
                'unit_cost' => 120000.00,
            ],

            // Vaisselle
            [
                'name' => 'Assiettes plates porcelaine liseré or',
                'category' => InventoryItem::CATEGORY_TABLEWARE,
                'unit' => 'pièce',
                'quantity_total' => 500,
                'quantity_available' => 450,
                'quantity_alert' => 80,
                'description' => 'Porcelaine blanche de prestige avec double filet doré.',
                'location' => 'Office traiteur - Caisses V1 à V10',
                'unit_cost' => 750.00,
            ],
            [
                'name' => 'Verres à pied en cristal gravé',
                'category' => InventoryItem::CATEGORY_TABLEWARE,
                'unit' => 'pièce',
                'quantity_total' => 480,
                'quantity_available' => 410,
                'quantity_alert' => 60,
                'description' => 'Verres à eau et jus en cristal pour table d’honneur et invités.',
                'location' => 'Office traiteur - Racks verres',
                'unit_cost' => 600.00,
            ],
            [
                'name' => 'Ménagère complète couverts dorés (Fourchette, Couteau, Cuillère)',
                'category' => InventoryItem::CATEGORY_TABLEWARE,
                'unit' => 'lot',
                'quantity_total' => 450,
                'quantity_available' => 400,
                'quantity_alert' => 50,
                'description' => 'Couverts en acier inoxydable doré brossé.',
                'location' => 'Office traiteur - Tiroirs sécurisés',
                'unit_cost' => 1200.00,
            ],

            // Linge de table
            [
                'name' => 'Nappes rondes blanches satinées (Ø 300cm)',
                'category' => InventoryItem::CATEGORY_LINEN,
                'unit' => 'pièce',
                'quantity_total' => 55,
                'quantity_available' => 42,
                'quantity_alert' => 15,
                'description' => 'Retombée totale au sol, repassage facile, anti-tâche.',
                'location' => 'Lingerie - Armoire 1',
                'unit_cost' => 3500.00,
            ],
            [
                'name' => 'Serviettes de table assorties blanches brodées',
                'category' => InventoryItem::CATEGORY_LINEN,
                'unit' => 'pièce',
                'quantity_total' => 500,
                'quantity_available' => 430,
                'quantity_alert' => 70,
                'description' => 'Serviettes 50x50cm coton satiné.',
                'location' => 'Lingerie - Armoire 2',
                'unit_cost' => 350.00,
            ],

            // Éclairage & Effets
            [
                'name' => 'Projecteurs PAR LED RGBW sans fil sur batterie',
                'category' => InventoryItem::CATEGORY_LIGHTING,
                'unit' => 'pièce',
                'quantity_total' => 24,
                'quantity_available' => 18,
                'quantity_alert' => 6,
                'description' => 'Projecteurs d’ambiance murale contrôlables par DMX/télécommande.',
                'location' => 'Régie technique - Flight cases L1',
                'unit_cost' => 14000.00,
            ],
            [
                'name' => 'Machine à étincelles froides (Cold Sparkler)',
                'category' => InventoryItem::CATEGORY_LIGHTING,
                'unit' => 'pièce',
                'quantity_total' => 4,
                'quantity_available' => 3,
                'quantity_alert' => 2,
                'description' => 'Effet pyrotechnique intérieur sans danger pour ouverture de bal.',
                'location' => 'Régie technique - Flight case FX',
                'unit_cost' => 45000.00,
            ],

            // Sonorisation
            [
                'name' => 'Paire d’enceintes amplifiées 1000W + Trépieds',
                'category' => InventoryItem::CATEGORY_AUDIO,
                'unit' => 'lot',
                'quantity_total' => 4,
                'quantity_available' => 3,
                'quantity_alert' => 1,
                'description' => 'Enceintes de sonorisation d’appoint pour terrasse ou salle secondaire.',
                'location' => 'Régie son',
                'unit_cost' => 85000.00,
            ],
            [
                'name' => 'Set de microphones HF sans fil double canal',
                'category' => InventoryItem::CATEGORY_AUDIO,
                'unit' => 'lot',
                'quantity_total' => 4,
                'quantity_available' => 4,
                'quantity_alert' => 2,
                'description' => 'Micros main pour animateurs et DJ.',
                'location' => 'Régie son - Valise HF',
                'unit_cost' => 28000.00,
            ],

            // Décoration
            [
                'name' => 'Arche florale hexagonale d’honneur avec fleurs artificielles premium',
                'category' => InventoryItem::CATEGORY_DECORATION,
                'unit' => 'pièce',
                'quantity_total' => 2,
                'quantity_available' => 1,
                'quantity_alert' => 1,
                'description' => 'Grande arche démontable pour coin photos et entrée des mariés.',
                'location' => 'Stock déco - Zone B',
                'unit_cost' => 55000.00,
            ],
            [
                'name' => 'Centres de table candélabres dorés 5 branches',
                'category' => InventoryItem::CATEGORY_DECORATION,
                'unit' => 'pièce',
                'quantity_total' => 40,
                'quantity_available' => 35,
                'quantity_alert' => 8,
                'description' => 'Chandelier 75cm finition or brillant avec coupelles photophores.',
                'location' => 'Stock déco - Rayon C3',
                'unit_cost' => 6500.00,
            ],
        ];

        foreach ($items as $data) {
            $item = InventoryItem::create(array_merge($data, [
                'business_id' => $business->id,
                'is_active'   => true,
                'created_by'  => $userId,
            ]));

            // Initial stock intake movement
            InventoryMovement::create([
                'business_id' => $business->id,
                'inventory_item_id' => $item->id,
                'type' => InventoryMovement::TYPE_IN,
                'quantity' => $item->quantity_total,
                'quantity_before' => 0,
                'quantity_after' => $item->quantity_total,
                'reason' => 'Inventaire initial et mise en stock',
                'created_by' => $userId,
            ]);

            // If some are reserved/in use
            $diff = $item->quantity_total - $item->quantity_available;
            if ($diff > 0) {
                InventoryMovement::create([
                    'business_id' => $business->id,
                    'inventory_item_id' => $item->id,
                    'type' => InventoryMovement::TYPE_RESERVE,
                    'quantity' => $diff,
                    'quantity_before' => $item->quantity_total,
                    'quantity_after' => $item->quantity_available,
                    'reason' => 'Affectation pour les événements du weekend',
                    'created_by' => $userId,
                ]);
            }
        }
    }
}
