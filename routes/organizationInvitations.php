<?php

use Illuminate\Support\Facades\Route;

Route::put('/organizations/invitation/{token}', 'Organization\OrganizationInvitationApiController@update')->name('organizations.invitation.update');
