<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsForeignTable extends Migration
{
    public function up()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->foreign('option_category_id')->references('id')->on('option_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropForeign(['option_category_id']);
        });
    }
}
