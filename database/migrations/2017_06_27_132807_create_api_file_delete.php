<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiFileDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_file_delete', function($table)
        {
            $table->string('file_id', 32)->primary()->comment('檔案id');
            $table->string('base_name', 30)->nullable()->comment('檔案名稱'); 
            $table->string('base_description', 200)->nullable()->comment('檔案描述');
            $table->string('previous', 32)->nullable()->comment('上一版檔案id');
            $table->string('store_type', 1)->nullable()->comment('儲存方式,C=code;P=path;B=both'); 
            $table->string('name', 30)->nullable()->comment('檔名'); 
            $table->string('extension', 30)->nullable()->comment('副檔名');
            $table->string('mime', 30)->nullable()->comment('檔案MIME');
            $table->string('path', 200)->nullable()->comment('檔案存放路徑');
            $table->string('transform', 60)->nullable()->comment('檔案存放名稱');
            $table->string('deleted_by', 10)->nullable();
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('api_file_delete');
    }
}
