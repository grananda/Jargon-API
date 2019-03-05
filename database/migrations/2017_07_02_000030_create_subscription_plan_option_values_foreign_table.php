<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlanOptionValuesForeignTable extends Migration
{
    public function up()
    {
        Schema::table('subscription_plan_option_values', function (Blueprint $table) {
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('option_key')->references('option_key')->on('subscription_options')->onDelete('cascade');
        });
    }

    public function down()
    {
        if (Schema::hasTable('subscription_plan_option_values')) {
            Schema::table('subscription_plan_option_values', function (Blueprint $table) {
                $table->dropForeign(['subscription_plan_id']);
                $table->dropForeign(['option_key']);
            });
        }
    }
}
