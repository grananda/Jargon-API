<?php

namespace App\Providers;

use App\Models\Organization;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Policies\OrganizationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SubscriptionPlanPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Organization::class     => OrganizationPolicy::class,
        Team::class             => TeamPolicy::class,
        Project::class          => ProjectPolicy::class,
        SubscriptionPlan::class => SubscriptionPlanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
