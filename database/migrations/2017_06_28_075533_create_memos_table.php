<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemosTable extends Migration
{
    public function up()
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->longText('body');
            $table->string('status')->default('draft');
            $table->string('item_token')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->index('status');
            $table->index('subject');
            $table->unique('item_token');
        });
    }

    public function down()
    {
        Schema::dropIfExists('memos');
    }
}
