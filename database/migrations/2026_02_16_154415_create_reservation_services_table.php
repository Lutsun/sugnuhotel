<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/[timestamp]_create_reservation_services_table.php
public function up()
{
    Schema::create('reservation_services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
        $table->foreignId('service_id')->constrained();
        $table->integer('quantity')->default(1);
        $table->decimal('price', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_services');
    }
};
