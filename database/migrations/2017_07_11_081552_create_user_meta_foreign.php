<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMetaForeign extends Migration
{
    public function up()
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
}
