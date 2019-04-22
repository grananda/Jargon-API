<?php

use Illuminate\Support\Facades\Route;

Route::post('/webhooks/stripe', 'Webhook\StripeWebHookController@index')->name('webhooks.stripe.index');
