<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE order_items DROP CONSTRAINT IF EXISTS order_items_type_check");
        DB::statement("ALTER TABLE order_items ADD CONSTRAINT order_items_type_check CHECK (type IN ('part', 'service', 'labor', 'other'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE order_items DROP CONSTRAINT IF EXISTS order_items_type_check");
        DB::statement("ALTER TABLE order_items ADD CONSTRAINT order_items_type_check CHECK (type IN ('part', 'service', 'labor'))");
    }
};
