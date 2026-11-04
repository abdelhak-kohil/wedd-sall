<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Operational Preparation Tasks (Checklists)
        Schema::create('event_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('salle_mobilier'); // salle_mobilier, decoration, technique, traiteur, accueil_securite, cloture_nettoyage
            $table->string('timing_phase')->default('DAY_OF_SETUP'); // J_MINUS_2, J_MINUS_1, DAY_OF_SETUP, DURING_EVENT, POST_EVENT
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->boolean('is_critical')->default(false);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['business_id', 'reservation_id']);
            $table->index(['reservation_id', 'status']);
        });

        // 2. External Event Vendors (Prestataires Externes)
        Schema::create('event_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->string('category'); // traiteur, dj_orchestre, photographe, decoration, neguafa, patisserie, autre
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('phone');
            $table->string('arrival_time')->nullable(); // e.g. "16:00"
            $table->text('special_notes')->nullable();
            $table->boolean('is_confirmed')->default(true);
            $table->timestamps();

            $table->index(['business_id', 'reservation_id']);
        });

        // 3. Day-of Chronological Timeline / Run of Show
        Schema::create('event_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->string('time'); // e.g. "17:30"
            $table->string('title'); // e.g. "Accueil des invités & cocktail de bienvenue"
            $table->text('description')->nullable();
            $table->string('person_in_charge')->nullable();
            $table->boolean('is_done')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['business_id', 'reservation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_timelines');
        Schema::dropIfExists('event_vendors');
        Schema::dropIfExists('event_tasks');
    }
};
