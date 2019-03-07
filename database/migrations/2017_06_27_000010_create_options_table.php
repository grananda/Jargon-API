<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('option_category_id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('option_key');
            $table->string('option_value');
            $table->string('option_scope')->default('user');
            $table->string('option_type')->default('check');
            $table->string('option_enum')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });

        Schema::table('options', function (Blueprint $table) {
            $table->index('option_scope');
            $table->index('is_private');
            $table->unique('option_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
}
