<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlanOptionValuesTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_plan_option_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subscription_plan_id');
            $table->string('option_key');
            $table->string('option_value')->nullable();

            $table->timestamps();
        });

        Schema::table('subscription_plan_option_values', function (Blueprint $table) {
            $table->index('subscription_plan_id');
            $table->index('option_key');
            $table->index('option_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plan_option_values');
    }
}
