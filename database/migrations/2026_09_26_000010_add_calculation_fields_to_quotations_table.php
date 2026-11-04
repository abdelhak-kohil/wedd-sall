<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('quotations') && !Schema::hasColumn('quotations', 'subtotal')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->decimal('subtotal', 12, 2)->default(0.00)->after('package_id');
                $table->string('discount_type', 20)->default('none')->after('subtotal'); // none, fixed, percent
                $table->decimal('discount_value', 12, 2)->default(0.00)->after('discount_type');
                $table->decimal('discount_amount', 12, 2)->default(0.00)->after('discount_value');
                $table->decimal('tax_rate', 5, 2)->default(0.00)->after('discount_amount'); // e.g. 0, 9, 19
                $table->decimal('tax_amount', 12, 2)->default(0.00)->after('tax_rate');
                $table->json('items')->nullable()->after('tax_amount');
                $table->text('payment_terms')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_type',
                'discount_value',
                'discount_amount',
                'tax_rate',
                'tax_amount',
                'items',
                'payment_terms',
            ]);
        });
    }
};
