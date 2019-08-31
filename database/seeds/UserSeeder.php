<?php

use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasDeleted;
use App\Models\Options\Option;
use App\Models\Options\OptionUser;
use App\Models\Role;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
            'users',
            'role_user',
            'collaborators',
            'user_profiles',
            'option_users',
            'active_subscriptions',
            'active_subscription_option_values',
        ]);

        Event::fake([
            UserActivationTokenGenerated::class,
            UserWasDeleted::class,
        ]);

        $users = $this->getSeedFileContents('users');

        $options = Option::where('option_scope', 'user')
            ->where('option_scope', Option::USER_OPTION)
            ->get()
        ;

        foreach ($users as $user) {
            /** @var \App\Models\Role $role */
            $role = Role::findByAliasOrFail($user['role']);

            /** @var \App\Models\User $userItem */
            $userItem = factory(User::class)->create([
                'name'     => $user['name'],
                'email'    => $user['email'],
                'password' => $user['password'],
            ]);
            $userItem->setRole($role);

            factory(UserProfile::class)->create([
                'username' => $user['username'],
                'user_id'  => $userItem->id,
            ]);

            foreach ($options as $option) {
                factory(OptionUser::class)->create([
                    'user_id'      => $userItem->id,
                    'option_value' => $option->option_value,
                    'option_key'   => $option->option_key,
                ]);
            }

            if (isset($user['subscription'])) {
                /** @var SubscriptionPlan | null $subscriptionPlan */
                $subscriptionPlan = SubscriptionPlan::findByAliasOrFail($user['subscription']);

                /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
                $activeSubscription = factory(ActiveSubscription::class)->create([
                    'user_id'              => $userItem->id,
                    'subscription_plan_id' => $subscriptionPlan->id,
                    'subscription_active'  => true,
                ]);

                foreach ($subscriptionPlan->options as $option) {
                    factory(ActiveSubscriptionOptionValue::class)->create([
                        'active_subscription_id' => $activeSubscription->id,
                        'option_key'             => $option->option_key,
                        'option_value'           => $option->option_value,
                    ]);
                }
            }
        }
    }
}
