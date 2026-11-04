<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first();
        if (!$business) {
            return;
        }

        if (Package::where('business_id', $business->id)->count() > 0) {
            return;
        }

        $packages = [
            [
                'name_fr' => 'Pack Essentiel',
                'name_ar' => 'الباقة الكلاسيكية',
                'description_fr' => 'Idéal pour fiançailles, khitana ou réceptions intimes.',
                'description_ar' => 'مثالية للخطوبة، الختان أو الحفلات العائلية البسيطة.',
                'base_price' => 180000.00,
                'price_per_guest' => 0.00,
                'included_items' => [
                    'Location de la salle climatisée / chauffée',
                    'Tables rondes et chaises avec housses et nœuds',
                    'Podium des mariés avec trône classique',
                    'Équipe d\'accueil et de sécurité à l\'entrée',
                    'Nettoyage avant et après événement',
                ],
                'is_active' => true,
            ],
            [
                'name_fr' => 'Pack Prestige',
                'name_ar' => 'باقة البرستيج',
                'description_fr' => 'Le choix n°1 des mariages algériens modernes.',
                'description_ar' => 'الخيار الأكثر طلباً للأعراس مع هندسة صوتية وإضاءة احترافية.',
                'base_price' => 280000.00,
                'price_per_guest' => 0.00,
                'included_items' => [
                    'Tous les avantages du Pack Essentiel',
                    'Sonorisation professionnelle & DJ animateur',
                    'Jeux de lumières dynamiques & Machine à fumée lourde pour l\'entrée',
                    'Trône royal moderne avec arche florale illuminée',
                    'Camérawoman dédiée dans la salle des femmes',
                    'Salon privé réservé pour les retouches de la mariée',
                ],
                'is_active' => true,
            ],
            [
                'name_fr' => 'Pack Impérial VIP',
                'name_ar' => 'الباقة الملكية الشاملة VIP',
                'description_fr' => 'L\'excellence absolue avec service traiteur thé/café et couverture vidéo premium.',
                'description_ar' => 'التميز الفاخر مع خدمة القهوة والشاي والتصوير عالي الدقة وسويت العروسة.',
                'base_price' => 420000.00,
                'price_per_guest' => 800.00,
                'included_items' => [
                    'Tous les avantages du Pack Prestige',
                    'Couverture vidéo multi-caméras 4K & Drone d\'intérieur',
                    'Entrée traditionnelle (Zorna / Cortège d\'honneur)',
                    'Service thé à la menthe & café traditionnel à volonté',
                    'Serveurs et serveuses professionnels en tenue d\'apparat (1 pour 30 invités)',
                    'Suite VIP grand confort pour les mariés avec salle de bain',
                    'Table d\'honneur personnalisée avec fleurs fraîches',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::create(array_merge($pkg, ['business_id' => $business->id]));
        }
    }
}
