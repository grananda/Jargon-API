<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveSubscriptionOptionValuesForeignTable extends Migration
{
    public function up()
    {
        Schema::table('active_subscription_option_values', function (Blueprint $table) {
            $table->foreign('active_subscription_id')->references('id')->on('active_subscriptions')->onDelete('cascade');
            $table->foreign('option_key')->references('option_key')->on('subscription_options')->onDelete('cascade');
        });
    }

    public function down()
    {
        if (Schema::hasTable('active_subscription_option_values')) {
            Schema::table('active_subscription_option_values', function (Blueprint $table) {
                $table->dropForeign(['active_subscription_id']);
                $table->dropForeign(['option_key']);
            });
        }
    }
}
