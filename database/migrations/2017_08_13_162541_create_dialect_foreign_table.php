<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDialectForeignTable extends Migration
{
    public function up()
    {
        Schema::table('dialects', function (Blueprint $table) {
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('dialects', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
        });
    }
}
