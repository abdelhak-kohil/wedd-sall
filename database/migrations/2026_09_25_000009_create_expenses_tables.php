<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name_fr', 100);
            $table->string('name_ar', 100)->nullable();
            $table->string('color', 20)->default('#64748b'); // tailwind slate-500
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('reference', 50)->nullable();          // expense ref / receipt #
            $table->string('vendor', 150)->nullable();            // supplier / merchant
            $table->text('description');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_method', 20)->default('CASH'); // CASH|CHEQUE|TRANSFER|OTHER
            $table->string('cheque_number', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'expense_date']);
            $table->index(['business_id', 'expense_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};
