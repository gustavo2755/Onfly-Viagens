<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->index(['status', 'departure_date'], 'travel_orders_user_status_departure_idx');
            $table->index('departure_date', 'travel_orders_departure_date_idx');
            $table->index('created_at', 'travel_orders_created_at_idx');
            $table->index('destination', 'travel_orders_destination_idx');
        });
    }

    public function down(): void
    {
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->dropIndex('travel_orders_user_status_departure_idx');
            $table->dropIndex('travel_orders_departure_date_idx');
            $table->dropIndex('travel_orders_created_at_idx');
            $table->dropIndex('travel_orders_destination_idx');
        });
    }
};
