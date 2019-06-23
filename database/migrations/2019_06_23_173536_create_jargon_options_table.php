<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJargonOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jargon_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language')->default('php');
            $table->string('file_ext')->default('php');
            $table->string('i18n_path')->default('resources/lang/');
            $table->string('framework')->default('laravel');
            $table->string('translation_file_mode')->default('array');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jargon_options');
    }
}
