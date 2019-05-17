<?php

use Illuminate\Support\Facades\Route;

Route::put('/projects/invitation/{token}', 'Project\ProjectInvitationController@update')->name('projects.invitation.update');
