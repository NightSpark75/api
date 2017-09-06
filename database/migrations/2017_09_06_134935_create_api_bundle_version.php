<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiApkVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_bundle_version', function($table)
        {
            // ex: version 1.23.1 = 1001023001
            $table->string('version', 32)->primary()->comment('版本號');
            $table->longText('bundle_file')->nullable()->comment('base64編碼');
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
        Schema::dropIfExists('api_bundle_version');
    }
}
