<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('company')->nullable();
            $table->string('occupation')->nullable();
            $table->longText('biography')->nullable();
            $table->string('web_url')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_git')->nullable();
            $table->timestamps();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->index('username');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
