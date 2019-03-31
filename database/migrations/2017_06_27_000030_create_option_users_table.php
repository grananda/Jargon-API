<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionUsersTable extends Migration
{
    public function up()
    {
        Schema::create('option_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->string('option_key');
            $table->string('option_value');
            $table->timestamps();
        });

        Schema::table('option_users', function (Blueprint $table) {
            $table->index('option_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('option_users');
    }
}
