<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE order_items MODIFY COLUMN type ENUM('part', 'service', 'labor', 'other') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE order_items MODIFY COLUMN type ENUM('part', 'service', 'labor') NOT NULL");
    }
};
