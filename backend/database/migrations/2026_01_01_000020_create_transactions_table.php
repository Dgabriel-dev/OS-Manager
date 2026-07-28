<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']);
            $table->foreignId('financial_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'pix', 'bank_transfer', 'other']);
            $table->date('payment_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled', 'overdue']);
            $table->timestamp('paid_at')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('payment_date');
            $table->index('status');
            $table->index('financial_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
