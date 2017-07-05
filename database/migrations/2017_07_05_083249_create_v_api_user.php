<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVApiUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("
            CREATE VIEW v_api_user AS
                select 'ppm' sys, co, user_id id, user_name name, user_pw pwd, class, state
                    from ppmadm.sma_user_m
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement('DROP VIEW IF EXISTS v_api_user');
    }
}
