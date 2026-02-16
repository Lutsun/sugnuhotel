<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   
public function up()
{
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('room_number')->unique();
        $table->foreignId('room_type_id')->constrained();
        $table->integer('floor');
        $table->decimal('price_per_night', 10, 2);
        $table->integer('max_occupancy');
        $table->enum('status', ['available', 'occupied', 'maintenance', 'out_of_service'])->default('available');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
