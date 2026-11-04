<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $businessId = DB::table('businesses')->value('id');
        if (! $businessId) {
            return;
        }

        // Skip if already seeded
        if (DB::table('expense_categories')->where('business_id', $businessId)->exists()) {
            return;
        }

        $categories = [
            ['name_fr' => 'Salaires & Charges',    'name_ar' => 'الرواتب والأعباء',     'color' => '#6366f1'],
            ['name_fr' => 'Loyer & Charges locatives', 'name_ar' => 'الإيجار والأعباء', 'color' => '#8b5cf6'],
            ['name_fr' => 'Eau, Gaz & Électricité', 'name_ar' => 'الماء والغاز والكهرباء', 'color' => '#0ea5e9'],
            ['name_fr' => 'Entretien & Réparations', 'name_ar' => 'الصيانة والإصلاحات', 'color' => '#f59e0b'],
            ['name_fr' => 'Fournitures & Consommables', 'name_ar' => 'المستلزمات', 'color' => '#10b981'],
            ['name_fr' => 'Communication & Internet', 'name_ar' => 'الاتصالات والإنترنت', 'color' => '#06b6d4'],
            ['name_fr' => 'Marketing & Publicité',  'name_ar' => 'التسويق والإعلان',    'color' => '#f43f5e'],
            ['name_fr' => 'Taxes & Impôts',         'name_ar' => 'الضرائب والرسوم',      'color' => '#dc2626'],
            ['name_fr' => 'Prestataires externes',  'name_ar' => 'مزودو الخدمات',        'color' => '#64748b'],
            ['name_fr' => 'Divers',                 'name_ar' => 'متفرقات',               'color' => '#94a3b8'],
        ];

        foreach ($categories as $cat) {
            DB::table('expense_categories')->insert(array_merge($cat, [
                'business_id' => $businessId,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));
        }
    }
}
