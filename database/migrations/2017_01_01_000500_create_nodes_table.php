<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodesTable extends Migration
{
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->string('key');
            $table->string('route')->nullable();
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedInteger('sort_index')->default(0)->nullable(true);
            $table->nestedSet();
            $table->timestamps();

            $table->index('key');
            $table->unique('uuid');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nodes');
    }
}
