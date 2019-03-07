<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFileUserProfileTable extends Migration
{
    public function up()
    {
        Schema::create('media_file_user_profile', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_profile_id');
            $table->unsignedInteger('media_file_id');
            $table->timestamps();
        });

        Schema::table('media_file_user_profile', function (Blueprint $table) {
            $table->index(['media_file_id', 'user_profile_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_file_user_profile');
    }
}
