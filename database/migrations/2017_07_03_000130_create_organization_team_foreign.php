<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationTeamForeign extends Migration
{
    public function up()
    {
        Schema::table('organization_team', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('organization_team', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['team_id']);
        });
    }
}
