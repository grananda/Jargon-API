<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToGitFileHashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('git_file_hashes', function (Blueprint $table) {
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
        Schema::table('git_file_hashes', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });
    }
}
