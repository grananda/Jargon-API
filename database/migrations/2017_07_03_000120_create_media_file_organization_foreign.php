<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFileOrganizationForeign extends Migration
{
    public function up()
    {
        Schema::table('media_file_organization', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('media_file_id')->references('id')->on('media_files')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('media_file_organization', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['media_file_id']);
        });
    }
}
