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
        Schema::table('fuelings', function (Blueprint $table) {
            $table->dropColumn('fuel_consumption');
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
            $table->unsignedDecimal('fuel_consumption', 6, 2)->nullable();
        });
    }
};
