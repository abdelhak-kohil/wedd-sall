<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Venues (Establishments)
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name_fr', 150);
            $table->string('name_ar', 150);
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Physical Spaces (Halls, Terraces, VIP rooms)
        Schema::create('venue_spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->string('name_fr', 150);
            $table->string('name_ar', 150);
            $table->unsignedInteger('capacity_min')->default(50);
            $table->unsignedInteger('capacity_max')->default(450);
            $table->decimal('base_price', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Configurable Time Slots
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name_fr', 100);
            $table->string('name_ar', 100);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_overnight')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Core Reservations (Calendar & Availability)
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('reservation_number', 30);
            $table->foreignId('venue_space_id')->constrained('venue_spaces')->onDelete('restrict');
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->onDelete('set null');
            
            // Client details inline for speed (linked to customers table in Phase 6)
            $table->string('customer_name', 150);
            $table->string('customer_phone', 30);
            $table->string('bride_name', 120)->nullable();
            $table->string('groom_name', 120)->nullable();
            $table->string('event_type', 50)->default('MARIAGE'); // MARIAGE, FIANCAILLES, HENNE, AQIQA, RECEPTION, AUTRE
            
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('expected_guests')->default(100);
            
            // Statuses
            $table->string('status', 30)->default('INQUIRY'); // INQUIRY, TEMPORARY_HOLD, CONFIRMED, COMPLETED, CANCELLED, ARCHIVED
            $table->string('payment_status', 30)->default('UNPAID'); // UNPAID, DEPOSIT_PAID, PARTIALLY_PAID, PAID, REFUNDED
            $table->timestamp('hold_expires_at')->nullable();
            
            // Financials
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->decimal('deposit_required', 12, 2)->default(0.00);
            $table->decimal('deposit_paid', 12, 2)->default(0.00);
            $table->decimal('total_paid', 12, 2)->default(0.00);
            $table->decimal('remaining_balance', 12, 2)->default(0.00);
            
            $table->text('internal_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['venue_space_id', 'event_date']);
            $table->index(['business_id', 'event_date']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('venue_spaces');
        Schema::dropIfExists('venues');
    }
};
