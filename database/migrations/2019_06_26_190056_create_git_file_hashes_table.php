<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitFileHashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_file_hashes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id');
            $table->string('locale');
            $table->string('file');
            $table->string('hash');
            $table->string('pull_request_number');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('git_file_hashes');
    }
}
