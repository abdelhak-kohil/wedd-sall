<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('register_date');
            $table->decimal('opening_balance', 12, 2)->default(0.00);
            $table->decimal('total_cash_in', 12, 2)->default(0.00);
            $table->decimal('total_cash_out', 12, 2)->default(0.00);
            $table->decimal('expected_closing_balance', 12, 2)->default(0.00);
            $table->decimal('actual_closing_balance', 12, 2)->nullable();
            $table->string('status', 20)->default('OPEN'); // OPEN, CLOSED
            $table->foreignId('opened_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'register_date']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_register_id')->nullable()->constrained('cash_registers')->nullOnDelete();
            $table->string('receipt_number', 50)->unique();
            $table->string('payment_type', 30); // DEPOSIT, INSTALLMENT, FINAL_BALANCE, REFUND
            $table->string('payment_method', 30); // CASH, CHEQUE, TRANSFER, OTHER
            $table->decimal('amount', 12, 2);
            $table->string('cheque_number', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->timestamps();

            $table->index(['business_id', 'payment_date']);
            $table->index(['reservation_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('cash_registers');
    }
};
