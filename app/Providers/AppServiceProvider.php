<?php

namespace App\Providers;

use App\Http\Resources\Projects\Project;
use App\Models\Organization;
use App\Models\Team;
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
            'projects'     => Project::class,
            'organization' => Organization::class,
            'team'         => Team::class,
        ]);
    }
}
