<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveSubscriptionOptionValuesTable extends Migration
{
    public function up()
    {
        Schema::create('active_subscription_option_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('active_subscription_id')->nullable(true);
            $table->string('option_key');
            $table->string('option_value')->nullable();

            $table->timestamps();
        });

        Schema::table('active_subscription_option_values', function (Blueprint $table) {
            $table->index('active_subscription_id');
            $table->index('option_key');
            $table->index('option_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('active_subscription_option_values');
    }
}
