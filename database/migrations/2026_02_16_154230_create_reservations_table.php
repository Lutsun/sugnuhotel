<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/[timestamp]_create_reservations_table.php
public function up()
{
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->string('reservation_number')->unique();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('room_id')->constrained();
        $table->date('check_in_date');
        $table->date('check_out_date');
        $table->integer('number_of_adults');
        $table->integer('number_of_children')->default(0);
        $table->decimal('total_price', 10, 2);
        $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending');
        $table->text('special_requests')->nullable();
        $table->timestamps();
        
        // Empêcher le double booking
        $table->unique(['room_id', 'check_in_date', 'check_out_date'], 'unique_booking');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
