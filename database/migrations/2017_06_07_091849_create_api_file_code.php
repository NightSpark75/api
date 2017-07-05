<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiFileCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_file_code', function($table)
        {
            $table->string('file_id', 32)->primary()->comment('檔案id');
            $table->string('name', 100)->nullable()->comment('檔名'); 
            $table->string('extension', 30)->nullable()->comment('副檔名');
            $table->string('mime', 30)->nullable()->comment('檔案MIME');
            $table->longText('code')->nullable()->comment('base64編碼');
            $table->string('path', 200)->nullable()->comment('檔案存放路徑');
            $table->string('transform', 60)->nullable()->comment('檔案存放名稱');
            $table->string('store_type', 1)->nullable()->comment('儲存方式,C=code;P=path;B=both');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
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
        Schema::dropIfExists('api_file_code');
    }
}
