<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVUserPrg extends Migration
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
            CREATE VIEW v_user_prg AS
                select pm.sys_id, pm.sys_name, pd.prg_id, pd.prg_name, user_id
                    from (
                        select substr(t.user_id, 2, length(t.user_id)) user_id, substr(t.data_d,1,8) prg_id
                            from sma_tree t    
                        union
                        select substr(r.user_id, 2, length(r.user_id)) user_id, substr(r.data_d,1,8) prg_id
                            from sma_tree_role r
                        union
                        select substr(u.user_id, 2, length(u.user_id)) user_id, substr(u.data_d,1,8) prg_id
                            from sma_tree_user u
                            order by 1, 2) w, sma_sys_prg_m pm, sma_sys_prg_d pd
                    where pm.sys_id = pd.sys_id and w.prg_id = pd.prg_id
                    order by user_id, sys_id, prg_id
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
        DB::statement('DROP VIEW IF EXISTS v_user_prg');
    }
}
