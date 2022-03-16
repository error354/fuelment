<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuelings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->unsignedDecimal('amount', 10, 2);
            $table->unsignedDecimal('mileage', 10, 2);
            $table->boolean('full');
            $table->unsignedDecimal('fuel_consumption', 6, 2)->nullable();
            $table->date('date');
            $table->unsignedDecimal('price', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuelings');
    }
};
