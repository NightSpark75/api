<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiWebPrg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_web_prg', function($table)
        {
            $table->string('co', 10)->comment('公司代號');
            $table->string('prg_id', 10)->comment('D2K程式編號');
            $table->string('web_route', 60)->comment('web route'); 
            $table->string('rmk', 400)->nullable()->comment('備註');
            $table->string('status', 1)->nullable()->comment('Y=enable, N=disable');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->primary(['co', 'prg_id']);
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
        //
        Schema::dropIfExists('api_web_prg');
    }
}
