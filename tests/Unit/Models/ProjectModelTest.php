<?php

namespace Tests\Unit\Models;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Models\Dialect;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Models\Translations\Project
 */
class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_will_have_members_and_and_owner_collaborator()
    {
        // Given
        Bus::fake(CollaboratorAddedToProject::class);

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        /** @var \Illuminate\Database\Eloquent\Collection $members */
        $members = factory(User::class, 10)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\Role $roleOwner */
        $roleOwner = Role::where('alias', Project::PROJECT_OWNER_ROLE_ALIAS)->first();

        /** @var \App\Models\Role $roleMember */
        $roleMember = Role::where('alias', Project::PROJECT_DEFAULT_ROLE_ALIAS)->first();

        // When
        $project->setOwner($owner);
        $members->each(function ($member) use ($project) {
            $project->setMember($member, Project::PROJECT_DEFAULT_ROLE_ALIAS);
        });

        // Then
        $this->assertDatabaseHas('collaborators', [
            'user_id'     => $owner->id,
            'entity_id'   => $project->id,
            'entity_type' => 'project',
            'is_owner'    => true,
            'is_valid'    => true,
            'role_id'     => $roleOwner->id,
        ]);

        $members->each(function ($member) use ($project, $roleMember) {
            $this->assertDatabaseHas('collaborators', [
                'user_id'     => $member->id,
                'entity_id'   => $project->id,
                'entity_type' => 'project',
                'is_owner'    => false,
                'is_valid'    => false,
                'role_id'     => $roleMember->id,
            ]);
        });
    }

    /** @test */
    public function a_project_will_have_valid_members_and_and_owner_collaborator()
    {
        // Given
        Bus::fake(CollaboratorAddedToProject::class);

        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();
        $this->signIn($owner);

        /** @var \Illuminate\Database\Eloquent\Collection $members */
        $members = factory(User::class, 10)->create();

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\Role $roleOwner */
        $roleOwner = Role::where('alias', Project::PROJECT_OWNER_ROLE_ALIAS)->first();

        /** @var \App\Models\Role $roleMember */
        $roleMember = Role::where('alias', Project::PROJECT_DEFAULT_ROLE_ALIAS)->first();

        // When
        $project->setOwner($owner);
        $members->each(function ($member) use ($project) {
            $project->setMember($member, Project::PROJECT_DEFAULT_ROLE_ALIAS);
            $project->validateMember($member);
        });

        // Then
        $this->assertDatabaseHas('collaborators', [
            'user_id'     => $owner->id,
            'entity_id'   => $project->id,
            'entity_type' => 'project',
            'is_owner'    => true,
            'is_valid'    => true,
            'role_id'     => $roleOwner->id,
        ]);

        $members->each(function ($member) use ($project, $roleMember) {
            $this->assertDatabaseHas('collaborators', [
                'user_id'     => $member->id,
                'entity_id'   => $project->id,
                'entity_type' => 'project',
                'is_owner'    => false,
                'is_valid'    => true,
                'role_id'     => $roleMember->id,
            ]);
        });
    }

    /** @test */
    public function a_project_can_be_added()
    {
        // Given
        /** @var SubscriptionPlan | null $subscriptionPlan */
        $subscriptionPlan = SubscriptionPlan::findByAliasOrFail('professional-month-eur');

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user');

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = factory(ActiveSubscription::class)->create([
            'user_id'              => $user->id,
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

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($user);

        /** @var \App\Models\Dialect $dialect */
        $dialect = Dialect::inRandomOrder()->first();

        // When
        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($user);
        $project->setOrganization($organization);
        $project->setTeams([$team->id]);
        $project->setDialects([$dialect->id => ['is_default' => true]]);

        // Then
        $this->assertSame($organization->uuid, $project->organization->uuid);
        $this->assertSame($team->uuid, $project->teams->first()->uuid);
        $this->assertSame($dialect->id, $project->dialects()->first()->id);
        $this->assertDatabaseHas('dialect_project', [
            'project_id' => $project->id,
            'dialect_id' => $dialect->id,
            'is_default' => true,
        ]);
    }
}
