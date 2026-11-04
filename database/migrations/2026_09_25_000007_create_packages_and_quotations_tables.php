<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->constrained()->cascadeOnDelete();
                $table->string('name_fr', 150);
                $table->string('name_ar', 150)->nullable();
                $table->text('description_fr')->nullable();
                $table->text('description_ar')->nullable();
                $table->decimal('base_price', 12, 2)->default(0.00);
                $table->decimal('price_per_guest', 12, 2)->default(0.00);
                $table->json('included_items')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['business_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->constrained()->cascadeOnDelete();
                $table->string('quote_number', 50)->unique();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('customer_name', 150);
                $table->string('customer_phone', 30);
                $table->string('bride_name', 120)->nullable();
                $table->string('groom_name', 120)->nullable();
                $table->foreignId('venue_space_id')->constrained()->cascadeOnDelete();
                $table->date('event_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->integer('expected_guests')->default(100);
                $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
                $table->decimal('total_price', 12, 2)->default(0.00);
                $table->decimal('deposit_required', 12, 2)->default(0.00);
                $table->string('status', 30)->default('DRAFT'); // DRAFT, SENT, ACCEPTED, EXPIRED, DECLINED
                $table->date('valid_until');
                $table->text('notes')->nullable();
                $table->foreignId('converted_reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['business_id', 'status']);
                $table->index(['business_id', 'event_date']);
            });
        }

        // Add package_id to reservations table
        if (!Schema::hasColumn('reservations', 'package_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('packages');
    }
};
