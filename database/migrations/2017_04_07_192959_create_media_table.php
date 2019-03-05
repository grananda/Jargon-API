<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('media_key')->nullable();
            $table->string('path');
            $table->string('mime_type');
            $table->string('item_token');
            $table->timestamps();
        });

        Schema::table('media_files', function (Blueprint $table) {
            $table->unique('item_token');
            $table->unique('path');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_files');
    }
}
