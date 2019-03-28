<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_options', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('title');
            $table->string('description');
            $table->string('description_template');
            $table->string('option_key');
            $table->timestamps();
        });

        Schema::table('subscription_options', function (Blueprint $table) {
            $table->unique('option_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_options');
    }
}
