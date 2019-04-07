<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('active_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable(true)->unique(true);
            $table->unsignedInteger('subscription_plan_id')->nullable(true);
            $table->boolean('subscription_active')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::table('active_subscriptions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('subscription_plan_id');
            $table->index('subscription_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('active_subscriptions');
    }
}
