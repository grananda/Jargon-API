<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDialectTable extends Migration
{
    public function up()
    {
        Schema::create('dialects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('locale');
            $table->unsignedInteger('language_id');
            $table->string('country')->nullable();
            $table->string('country_key')->nullable();
            $table->timestamps();
        });

        Schema::table('dialects', function (Blueprint $table) {
            $table->index('name');
            $table->unique('locale');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dialects');
    }
}
