<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveSubscriptionsForeignTable extends Migration
{
    public function up()
    {
        Schema::table('active_subscriptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
        });
    }

    public function down()
    {
        if (Schema::hasTable('active_subscriptions')) {
            Schema::table('active_subscriptions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['subscription_plan_id']);
            });
        }
    }
}
