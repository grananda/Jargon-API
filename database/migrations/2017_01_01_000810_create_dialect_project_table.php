<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDialectProjectTable extends Migration
{
    public function up()
    {
        Schema::create('dialect_project', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('dialect_id');
            $table->timestamps();

            $table->index('is_default');
            $table->unique(['project_id', 'dialect_id']);

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('dialect_id')->references('id')->on('dialects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dialect_project');
    }
}
