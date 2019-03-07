<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionUsersForeignTable extends Migration
{
    public function up()
    {
        Schema::table('option_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('option_key')->references('option_key')->on('options')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('option_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['option_key']);
        });
    }
}
