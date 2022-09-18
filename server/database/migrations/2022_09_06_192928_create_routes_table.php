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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('fuelings', function(Blueprint $table)
        {
            $table->foreignId('route_id')->constrained()->onDelete('cascade')->after('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fuelings', function (Blueprint $table) {
            $table->dropColumn('route_id');
        });

        Schema::dropIfExists('routes');
    }
};
