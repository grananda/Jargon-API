<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTeamForeign extends Migration
{
    public function up()
    {
        Schema::table('project_team', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('project_team', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['project_id']);
        });
    }
}
