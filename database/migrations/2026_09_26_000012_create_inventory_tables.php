<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');                         // "Tables rondes 150cm"
            $table->string('category');                     // furniture|tableware|linen|audio|lighting|decoration|other
            $table->string('unit')->default('pcs');         // pcs, set, m, kg…
            $table->integer('quantity_total')->default(0);  // total owned
            $table->integer('quantity_available')->default(0); // computed: total - allocated
            $table->integer('quantity_alert')->default(5);  // alert threshold
            $table->text('description')->nullable();
            $table->string('location')->nullable();         // "Magasin 1 - Étagère B"
            $table->decimal('unit_cost', 12, 2)->nullable(); // purchase cost per unit
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');           // IN | OUT | ADJUST | RESERVE | RETURN
            $table->integer('quantity');      // always positive; type carries direction
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
    }
};
