<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemoUserForeign extends Migration
{
    public function up()
    {
        Schema::table('memo_user', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('memo_id')->references('id')->on('memos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('memo_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['memo_id']);
        });
    }
}
