<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionAppsForeignTable extends Migration
{
    public function up()
    {
        Schema::table('option_apps', function (Blueprint $table) {
            $table->foreign('option_key')->references('option_key')->on('options')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('option_apps', function (Blueprint $table) {
            $table->dropForeign(['option_key']);
        });
    }
}
