<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_number');
            $table->string('item_token');
            $table->unsignedInteger('subscription_id');
            $table->double('price')->default(0);
            $table->timestamp('invoice_date');
            $table->string('invoice_status')->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unique('item_token');
            $table->unique('invoice_number');
            $table->index('invoice_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
