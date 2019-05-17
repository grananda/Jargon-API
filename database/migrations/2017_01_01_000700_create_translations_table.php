<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->text('definition');
            $table->unsignedInteger('node_id')->nullable();
            $table->unsignedInteger('dialect_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['node_id', 'dialect_id']);

            $table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dialect_id')->references('id')->on('dialects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
