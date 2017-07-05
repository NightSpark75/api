<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiFileToken extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_file_token', function($table)
        {
            $table->string('file_token', 32)->primary()->comment('取檔token');
            $table->string('load_user', 32)->comment('檔案取用人員'); 
            $table->string('file_id', 32)->comment('檔案id');
            $table->string('status', 1)->nullable()->comment('G=Get Token, L=Loaded');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->index(['file_token', 'load_user', 'file_id']);
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
        Schema::dropIfExists('api_file_token');
    }
}
