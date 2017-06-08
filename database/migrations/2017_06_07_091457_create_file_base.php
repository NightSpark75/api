<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileBase extends Migration
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
            $table->string('id', 36)->primary();
            $table->string('name', 30)->nullable(); 
            $table->string('description', 200)->nullable();
            $table->string('previous', 36)->nullable();
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
