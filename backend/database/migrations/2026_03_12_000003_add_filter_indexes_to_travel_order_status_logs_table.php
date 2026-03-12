<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_order_status_logs', function (Blueprint $table) {
            $table->index(['travel_order_id', 'created_at'], 'tosl_travel_order_created_idx');
            $table->index(['admin_user_id', 'created_at'], 'tosl_admin_user_created_idx');
            $table->index(['to_status', 'created_at'], 'tosl_to_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('travel_order_status_logs', function (Blueprint $table) {
            $table->dropIndex('tosl_travel_order_created_idx');
            $table->dropIndex('tosl_admin_user_created_idx');
            $table->dropIndex('tosl_to_status_created_idx');
        });
    }
};
