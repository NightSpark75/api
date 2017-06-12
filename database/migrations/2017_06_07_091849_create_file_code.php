<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileCode extends Migration
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
            $table->string('file_id', 36)->primary()->comment('檔案id');
            $table->string('name', 30)->nullable()->comment('檔名'); 
            $table->string('file_name', 30)->nullable()->comment('副檔名');
            $table->string('mime', 30)->nullable()->comment('檔案MIME');
            $table->text('code')->nullable()->comment('base64編碼');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
