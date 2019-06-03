<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemosTable extends Migration
{
    public function up()
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->string('subject');
            $table->longText('body');
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->index('status');
            $table->index('subject');
        });
    }

    public function down()
    {
        Schema::dropIfExists('memos');
    }
}
