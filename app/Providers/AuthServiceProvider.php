<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\Options\Option;
use App\Models\Organization;
use App\Models\Subscriptions\SubscriptionOption;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Models\Team;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;
use App\Models\User;
use App\Policies\CardPolicy;
use App\Policies\NodePolicy;
use App\Policies\OptionPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SubscriptionOptionPolicy;
use App\Policies\SubscriptionPlanPolicy;
use App\Policies\SubscriptionProductPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TranslationPolicy;
use App\Policies\UserPolicy;
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
        Organization::class        => OrganizationPolicy::class,
        Team::class                => TeamPolicy::class,
        User::class                => UserPolicy::class,
        Project::class             => ProjectPolicy::class,
        SubscriptionPlan::class    => SubscriptionPlanPolicy::class,
        SubscriptionProduct::class => SubscriptionProductPolicy::class,
        SubscriptionOption::class  => SubscriptionOptionPolicy::class,
        Option::class              => OptionPolicy::class,
        Card::class                => CardPolicy::class,
        Node::class                => NodePolicy::class,
        Translation::class         => TranslationPolicy::class,
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
