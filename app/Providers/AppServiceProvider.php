<?php

namespace App\Providers;

use App\Models\Organization;
use App\Models\Team;
use App\Models\Translations\Project;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'project'     => Project::class,
            'organization' => Organization::class,
            'team'         => Team::class,
        ]);
    }
}
