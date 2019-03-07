<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('lang_key')->nullable();
            $table->timestamps();
        });

        Schema::table('languages', function (Blueprint $table) {
            $table->index('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
