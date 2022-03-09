<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::unprepared(
            "CREATE TRIGGER default_vehicle_order
            BEFORE INSERT ON user_vehicle FOR EACH ROW
            SET NEW.order = 
            IFNULL((SELECT MAX(`order`)
                    FROM `user_vehicle`
                    WHERE user_id = NEW.user_id) + 1, 0)"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER default_vehicle_order");
    }
};
