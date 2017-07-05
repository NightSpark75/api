<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiFileBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_file_base', function($table)
        {
            $table->string('id', 32)->primary()->comment('檔案id');
            $table->string('name', 100)->nullable()->comment('檔案名稱'); 
            $table->string('description', 200)->nullable()->comment('檔案描述');
            $table->string('previous', 32)->nullable()->comment('上一版檔案id');
            $table->string('status', 1)->nullable()->comment('C=Creating, S=Success Save');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->index(['id', 'previous']);
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
        Schema::dropIfExists('api_file_base');
    }
}
