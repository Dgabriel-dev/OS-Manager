<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->string('status')->default('opened');
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->decimal('final_value', 10, 2)->nullable();
            $table->integer('warranty_days')->default(30);
            $table->date('warranty_until')->nullable();
            $table->date('entry_date');
            $table->date('estimated_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_number');
            $table->index('client_id');
            $table->index('status');
            $table->index('technician_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
