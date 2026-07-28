<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['income', 'expense']);
            $table->foreignId('parent_id')->nullable()->constrained('financial_categories')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_categories');
    }
};
