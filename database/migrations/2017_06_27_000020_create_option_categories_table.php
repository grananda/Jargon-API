<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('option_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('option_categories');
    }
}
