<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFileUserProfileForeign extends Migration
{
    public function up()
    {
        Schema::table('media_file_user_profile', function (Blueprint $table) {
            $table->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('media_file_id')->references('id')->on('media_files')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('media_file_user_profile', function (Blueprint $table) {
            $table->dropForeign(['user_profile_id']);
            $table->dropForeign(['media_file_id']);
        });
    }
}
