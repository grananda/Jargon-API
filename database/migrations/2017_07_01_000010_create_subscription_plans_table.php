<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('subscription_product_id')->nullable();
            $table->string('alias')->unique();
            $table->double('amount')->default(0);
            $table->integer('sort_order')->default(0);
            $table->enum('interval', ['month', 'year'])->default('month');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('subscription_product_id')->references('id')->on('subscription_products')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}
