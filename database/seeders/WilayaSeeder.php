<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayaSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed if empty
        if (DB::table('wilayas')->count() > 0) {
            return;
        }

        $wilayas = [
            [1, '01', 'Adrar', 'أدرار'],
            [2, '02', 'Chlef', 'الشلف'],
            [3, '03', 'Laghouat', 'الأغواط'],
            [4, '04', 'Oum El Bouaghi', 'أم البواقي'],
            [5, '05', 'Batna', 'باتنة'],
            [6, '06', 'Béjaïa', 'بجاية'],
            [7, '07', 'Biskra', 'بسكرة'],
            [8, '08', 'Béchar', 'بشار'],
            [9, '09', 'Blida', 'البليدة'],
            [10, '10', 'Bouira', 'البويرة'],
            [11, '11', 'Tamanrasset', 'تمنراست'],
            [12, '12', 'Tébessa', 'تبسة'],
            [13, '13', 'Tlemcen', 'تلمسان'],
            [14, '14', 'Tiaret', 'تيارت'],
            [15, '15', 'Tizi Ouzou', 'تيزي وزو'],
            [16, '16', 'Alger', 'الجزائر'],
            [17, '17', 'Djelfa', 'الجلفة'],
            [18, '18', 'Jijel', 'جيجل'],
            [19, '19', 'Sétif', 'سطيف'],
            [20, '20', 'Saïda', 'سعيدة'],
            [21, '21', 'Skikda', 'سكيكدة'],
            [22, '22', 'Sidi Bel Abbès', 'سيدي بلعباس'],
            [23, '23', 'Annaba', 'عنابة'],
            [24, '24', 'Guelma', 'قالمة'],
            [25, '25', 'Constantine', 'قسنطينة'],
            [26, '26', 'Médéa', 'المدية'],
            [27, '27', 'Mostaganem', 'مستغانم'],
            [28, '28', 'M\'Sila', 'المسيلة'],
            [29, '29', 'Mascara', 'معسكر'],
            [30, '30', 'Ouargla', 'ورقلة'],
            [31, '31', 'Oran', 'وهران'],
            [32, '32', 'El Bayadh', 'البيض'],
            [33, '33', 'Illizi', 'إليزي'],
            [34, '34', 'Bordj Bou Arréridj', 'برج بوعريريج'],
            [35, '35', 'Boumerdès', 'بومرداس'],
            [36, '36', 'El Tarf', 'الطارف'],
            [37, '37', 'Tindouf', 'تندوف'],
            [38, '38', 'Tissemsilt', 'تيسمسيلت'],
            [39, '39', 'El Oued', 'الوادي'],
            [40, '40', 'Khenchela', 'خنشلة'],
            [41, '41', 'Souk Ahras', 'سوق أهراس'],
            [42, '42', 'Tipaza', 'تيبازة'],
            [43, '43', 'Mila', 'ميلة'],
            [44, '44', 'Aïn Defla', 'عين الدفلى'],
            [45, '45', 'Naâma', 'النعامة'],
            [46, '46', 'Aïn Témouchent', 'عين تموشنت'],
            [47, '47', 'Ghardaïa', 'غرداية'],
            [48, '48', 'Relizane', 'غليزان'],
            [49, '49', 'Timimoun', 'تيميمون'],
            [50, '50', 'Bordj Badji Mokhtar', 'برج باجي مختار'],
            [51, '51', 'Ouled Djellal', 'أولاد جلال'],
            [52, '52', 'Béni Abbès', 'بني عباس'],
            [53, '53', 'In Salah', 'عين صالح'],
            [54, '54', 'In Guezzam', 'عين قزام'],
            [55, '55', 'Touggourt', 'تقرت'],
            [56, '56', 'Djanet', 'جانت'],
            [57, '57', 'El M\'Ghair', 'المغير'],
            [58, '58', 'El Meniaa', 'المنيعة'],
        ];

        foreach ($wilayas as [$id, $code, $nameFr, $nameAr]) {
            DB::table('wilayas')->insert([
                'id' => $id,
                'code' => $code,
                'name_fr' => $nameFr,
                'name_ar' => $nameAr,
            ]);
        }

        // Seed key communes for Alger (16) and Oran (31)
        $communes = [
            // Alger
            [16, 'Alger Centre', 'الجزائر الوسطى', '16001'],
            [16, 'Bab El Oued', 'باب الواد', '16002'],
            [16, 'Hussein Dey', 'حسين داي', '16003'],
            [16, 'El Harrach', 'الحراش', '16004'],
            [16, 'Kouba', 'القبة', '16005'],
            [16, 'Bir Mourad Raïs', 'بئر مراد رايس', '16006'],
            [16, 'Hydra', 'حيدرة', '16007'],
            [16, 'Ben Aknoun', 'بن عكنون', '16008'],
            [16, 'Birkhadem', 'بئر خادم', '16009'],
            [16, 'Chéraga', 'شراقة', '16010'],
            [16, 'Bab Ezzouar', 'باب الزوار', '16011'],
            [16, 'Dar El Beïda', 'دار البيضاء', '16012'],
            [16, 'Rouiba', 'الرويبة', '16013'],
            [16, 'Reghaia', 'الرغاية', '16014'],
            [16, 'Bordj El Kiffan', 'برج الكيفان', '16015'],
            // Oran
            [31, 'Oran', 'وهران', '31001'],
            [31, 'Bir El Djir', 'بئر الجير', '31002'],
            [31, 'Es Sénia', 'السانية', '31003'],
            [31, 'Arzew', 'أرزيو', '31004'],
            // Constantine
            [25, 'Constantine', 'قسنطينة', '25001'],
            [25, 'El Khroub', 'الخروب', '25002'],
            [25, 'Hamma Bouziane', 'حامة بوزيان', '25003'],
            // Annaba
            [23, 'Annaba', 'عنابة', '23001'],
            [23, 'El Bouni', 'البوني', '23002'],
            // Blida
            [9, 'Blida', 'البليدة', '09001'],
            [9, 'Boufarik', 'بوفاريك', '09002'],
            [9, 'Meftah', 'مفتاح', '09003'],
            // Setif
            [19, 'Sétif', 'سطيف', '19001'],
            [19, 'El Eulma', 'العلمة', '19002'],
            // Tizi Ouzou
            [15, 'Tizi Ouzou', 'تيزي وزو', '15001'],
            [15, 'Azazga', 'عزازقة', '15002'],
            // Bejaia
            [6, 'Béjaïa', 'بجاية', '06001'],
            [6, 'Kherrata', 'خراطة', '06002'],
            // Batna
            [5, 'Batna', 'باتنة', '05001'],
            [5, 'Arris', 'أريس', '05002'],
        ];

        foreach ($communes as [$wilayaId, $nameFr, $nameAr, $postcode]) {
            DB::table('communes')->insert([
                'wilaya_id' => $wilayaId,
                'name_fr' => $nameFr,
                'name_ar' => $nameAr,
                'postcode' => $postcode,
            ]);
        }
    }
}
