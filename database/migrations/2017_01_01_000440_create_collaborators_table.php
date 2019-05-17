<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollaboratorsTable extends Migration
{
    public function up()
    {
        Schema::create('collaborators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('validation_token')->nullable(true);
            $table->morphs('entity');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->boolean('is_valid')->default(false);
            $table->boolean('is_owner')->default(false);
            $table->timestamps();

            $table->index('is_owner');
            $table->index('is_valid');
            $table->unique('validation_token');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('collaborators');
    }
}
