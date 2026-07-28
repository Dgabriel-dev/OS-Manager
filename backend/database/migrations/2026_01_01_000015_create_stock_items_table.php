<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('internal_code')->nullable()->unique();
            $table->string('barcode')->nullable();
            $table->string('description')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->integer('minimum_quantity')->default(5);
            $table->string('location')->nullable();
            $table->string('unit_of_measure')->default('un');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('barcode');
            $table->index('name');
            $table->index('stock_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
