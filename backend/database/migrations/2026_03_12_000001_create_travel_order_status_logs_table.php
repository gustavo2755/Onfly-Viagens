<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_order_id')->constrained('travel_orders')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_status', 30);
            $table->string('to_status', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_order_status_logs');
    }
};
