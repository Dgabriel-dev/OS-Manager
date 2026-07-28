<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('color')->nullable();
            $table->text('accessories_delivered')->nullable();
            $table->text('physical_state')->nullable();
            $table->text('reported_defect')->nullable();
            $table->text('technical_diagnosis')->nullable();
            $table->text('password_encrypted')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
