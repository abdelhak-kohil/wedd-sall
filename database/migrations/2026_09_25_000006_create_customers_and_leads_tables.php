<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Algerian 58 Wilayas
        if (!Schema::hasTable('wilayas')) {
            Schema::create('wilayas', function (Blueprint $table) {
                $table->unsignedSmallInteger('id')->primary();
                $table->string('code', 3)->unique();
                $table->string('name_fr', 100);
                $table->string('name_ar', 100);
            });
        }

        // 2. Communes
        if (!Schema::hasTable('communes')) {
            Schema::create('communes', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('wilaya_id');
                $table->string('name_fr', 120);
                $table->string('name_ar', 120);
                $table->string('postcode', 10)->nullable();

                $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('cascade');
                $table->index('wilaya_id');
            });
        }

        // 3. Customers CRM
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
                $table->string('customer_code', 30);
                $table->string('full_name', 150);
                $table->string('phone', 30);
                $table->string('second_phone', 30)->nullable();
                $table->string('whatsapp_number', 30)->nullable();
                $table->string('email', 150)->nullable();
                
                $table->unsignedSmallInteger('wilaya_id')->nullable();
                $table->foreign('wilaya_id')->references('id')->on('wilayas')->onDelete('set null');
                $table->foreignId('commune_id')->nullable()->constrained('communes')->onDelete('set null');
                $table->text('address_line')->nullable();

                $table->string('bride_name', 120)->nullable();
                $table->string('groom_name', 120)->nullable();
                $table->string('preferred_language', 5)->default('fr'); // fr, ar
                $table->string('source', 50)->default('WALK_IN'); // FACEBOOK, INSTAGRAM, TIKTOK, GOOGLE, RECOMMENDATION, WALK_IN, OTHER
                $table->text('internal_notes')->nullable();
                $table->timestamps();

                $table->unique(['business_id', 'customer_code']);
                $table->index(['business_id', 'phone']);
                $table->index(['business_id', 'full_name']);
            });
        }

        // 4. Add customer_id and cancellation tracking to reservations table
        if (!Schema::hasColumn('reservations', 'customer_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->foreignId('customer_id')->nullable()->after('reservation_number')->constrained('customers')->onDelete('restrict');
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
                $table->decimal('refund_amount', 12, 2)->default(0.00);
            });
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['customer_id', 'cancelled_at', 'cancellation_reason', 'cancelled_by', 'refund_amount']);
        });

        Schema::dropIfExists('customers');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('wilayas');
    }
};
