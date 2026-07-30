<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('service_orders')->where('status', 'opened')->update(['status' => 'open']);
    }

    public function down(): void
    {
        DB::table('service_orders')->where('status', 'open')->update(['status' => 'opened']);
    }
};
