<?php

namespace Tests\Unit\Services;

use App\Events\Collaborator\CollaboratorAddedToProject;
use App\Events\Collaborator\CollaboratorAddedToTeam;
use App\Exceptions\SubscriptionDowngradeRequirementException;
use App\Models\Organization;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionPlanOptionValue;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Models\Team;
use App\Models\Translations\Project;
use App\Models\User;
use App\Services\SubscriptionDowngradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group unit
 * @coversNothing
 */
class SubscriptionDowngradeServiceTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * @var \App\Services\SubscriptionDowngradeService
     */
    private $subscriptionDowngradeService;

    /**
     * @var \App\Models\Subscriptions\SubscriptionPlan
     */
    private $subscriptionPlan;

    public function setUp(): void
    {
        parent::setUp();

        Bus::fake(CollaboratorAddedToProject::class);
        Bus::fake(CollaboratorAddedToTeam::class);

        $this->user = $this->user();

        $this->createActiveSubscription($this->user, 'professional-month-eur', [
            'project_count'      => 10,
            'team_count'         => 10,
            'organization_count' => 10,
            'collaborator_count' => 10,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = factory(SubscriptionProduct::class)->create([
            'rank' => 10,
        ]);

        /* @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $this->subscriptionPlan = factory(SubscriptionPlan::class)->create([
            'subscription_product_id' => $subscriptionProduct->id,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $subscriptionPlanOptionValue */
        $subscriptionPlanOptionValue = factory(SubscriptionPlanOptionValue::class)->make([
            'option_key'   => 'organization_count',
            'option_value' => 5,
        ]);
        $this->subscriptionPlan->addOption($subscriptionPlanOptionValue);

        /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $subscriptionPlanOptionValue */
        $subscriptionPlanOptionValue = factory(SubscriptionPlanOptionValue::class)->make([
            'option_key'   => 'project_count',
            'option_value' => 5,
        ]);
        $this->subscriptionPlan->addOption($subscriptionPlanOptionValue);

        /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $subscriptionPlanOptionValue */
        $subscriptionPlanOptionValue = factory(SubscriptionPlanOptionValue::class)->make([
            'option_key'   => 'team_count',
            'option_value' => 5,
        ]);
        $this->subscriptionPlan->addOption($subscriptionPlanOptionValue);

        /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $subscriptionPlanOptionValue */
        $subscriptionPlanOptionValue = factory(SubscriptionPlanOptionValue::class)->make([
            'option_key'   => 'collaborator_count',
            'option_value' => 5,
        ]);
        $this->subscriptionPlan->addOption($subscriptionPlanOptionValue);

        $this->subscriptionDowngradeService = resolve(SubscriptionDowngradeService::class);
    }

    /** @test */
    public function an_exception_is_thrown_when_project_quota_blocks_a_downgrade()
    {
        // Given
        $this->expectException(SubscriptionDowngradeRequirementException::class);

        /** @var \Illuminate\Database\Eloquent\Collection $projects */
        $projects = factory(Project::class, 7)->create();
        $projects->each(function ($item) {
            /* @var \App\Models\Translations\Project $item */
            $item->setOwner($this->user);
        });

        // When
        $this->subscriptionDowngradeService->checkSubscriptionPlanDowngradeRules($this->user, $this->subscriptionPlan);
    }

    /** @test */
    public function an_exception_is_thrown_when_team_quota_blocks_a_downgrade()
    {
        // Given
        $this->expectException(SubscriptionDowngradeRequirementException::class);

        /** @var \Illuminate\Database\Eloquent\Collection $teams */
        $teams = factory(Team::class, 7)->create();
        $teams->each(function ($item) {
            /* @var \App\Models\Team $item */
            $item->setOwner($this->user);
        });

        // When
        $this->subscriptionDowngradeService->checkSubscriptionPlanDowngradeRules($this->user, $this->subscriptionPlan);
    }

    /** @test */
    public function an_exception_is_thrown_when_organization_quota_blocks_a_downgrade()
    {
        // Given
        $this->expectException(SubscriptionDowngradeRequirementException::class);

        /** @var \Illuminate\Database\Eloquent\Collection $organizations */
        $organizations = factory(Organization::class, 7)->create();
        $organizations->each(function ($item) {
            /* @var \App\Models\Organization $item */
            $item->setOwner($this->user);
        });

        // When
        $this->subscriptionDowngradeService->checkSubscriptionPlanDowngradeRules($this->user, $this->subscriptionPlan);
    }

    /** @test */
    public function an_exception_is_thrown_when_collaborator_quota_blocks_a_downgrade()
    {
        // Given
        $this->expectException(SubscriptionDowngradeRequirementException::class);

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($this->user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($this->user);

        /** @var \Illuminate\Database\Eloquent\Collection $members */
        $members = factory(User::class, 7)->create();
        $members->each(function ($item) use ($project, $team) {
            /* @var \App\Models\User $item */
            $project->setMember($item);
            $project->validateMember($item);

            $team->setMember($item);
            $team->validateMember($item);
        });

        // When
        $this->subscriptionDowngradeService->checkSubscriptionPlanDowngradeRules($this->user, $this->subscriptionPlan);
    }

    /** @test */
    public function a_subscription_plan_can_be_downgraded()
    {
        // Given
        /** @var \Illuminate\Database\Eloquent\Collection $projects */
        $projects = factory(Project::class, 3)->create();
        $projects->each(function ($item) {
            /* @var \App\Models\Translations\Project $item */
            $item->setOwner($this->user);
        });

        /** @var \Illuminate\Database\Eloquent\Collection $teams */
        $teams = factory(Team::class, 3)->create();
        $teams->each(function ($item) {
            /* @var \App\Models\Team $item */
            $item->setOwner($this->user);
        });

        /** @var \Illuminate\Database\Eloquent\Collection $organizations */
        $organizations = factory(Organization::class, 3)->create();
        $organizations->each(function ($item) {
            /* @var \App\Models\Organization $item */
            $item->setOwner($this->user);
        });

        /** @var \App\Models\Translations\Project $project */
        $project = factory(Project::class)->create();
        $project->setOwner($this->user);

        /** @var \Illuminate\Database\Eloquent\Collection $members */
        $members = factory(User::class, 3)->create();
        $members->each(function ($item) use ($project) {
            /* @var \App\Models\User $item */
            $project->setMember($item);
            $project->validateMember($item);
        });

        // When
        $response = $this->subscriptionDowngradeService->checkSubscriptionPlanDowngradeRules($this->user, $this->subscriptionPlan);

        // Then
        $this->assertTrue($response);
    }
}
