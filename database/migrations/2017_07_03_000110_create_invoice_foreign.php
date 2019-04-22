<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceForeign extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('subscription_id')->references('id')->on('active_subscriptions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
        });
    }
}
