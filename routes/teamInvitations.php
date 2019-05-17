<?php

use Illuminate\Support\Facades\Route;

Route::put('/teams/invitation/{token}', 'Team\TeamInvitationController@update')->name('teams.invitation.update');
