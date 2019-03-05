<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->string('title');
            $table->mediumText('description')->nullable();
            $table->unsignedInteger('organization_id')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->unique('uuid');

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
