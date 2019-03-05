<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationTeamTable extends Migration
{
    public function up()
    {
        Schema::create('organization_team', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('team_id');
            $table->timestamps();
        });

        Schema::table('organization_team', function (Blueprint $table) {
            $table->unique(['team_id', 'organization_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_team');
    }
}
