<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use App\Repositories\TeamRepository;
use App\Services\Team\TeamService;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit_Framework_MockObject_MockObject;
use Test\Traits\OrganizationApiTestTrait;
use Test\Traits\TeamApiTestTrait;
use Tests\TestCase;

class TeamServiceUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var  TeamRepository | PHPUnit_Framework_MockObject_MockObject */
    protected $teamRepository;

    /** @var  TeamService |PHPUnit_Framework_MockObject_MockObject */
    protected $teamService;

    public function setup()
    {
        parent::setUp();

        $this->teamRepository = $this->getMockBuilder(TeamRepository::class)
            ->setConstructorArgs([resolve(Connection::class), new Team()])
            ->setMethods(null)
            ->getMock();

        $this->teamService = $this->getMockBuilder(TeamService::class)
            ->setConstructorArgs([$this->teamRepository])
            ->setMethods(null)
            ->getMock();
    }

    /** @test */
    public function add_organization_to_team()
    {
        /** @var \App\Models\User $owner */
        $owner = factory(User::class)->create();

        /** @var \App\Models\Organization $organization_1 */
        $organization_1 = factory(Organization::class)->create();
        $organization_1->setOwner($owner);

        /** @var \App\Models\Organization $organization_2 */
        $organization_2 = factory(Organization::class)->create();
        $organization_2->setOwner($owner);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();
        $team->setOwner($owner);

        $this->actingAs($owner);

        $team = $this->teamService->setOrganizations($team, [
            $organization_1->id,
            $organization_2->id
        ]);

        $this->assertDatabaseHas('organization_team', [
            'team_id' => $team->id,
            'organization_id' => $organization_1->id,
        ]);

        $this->assertDatabaseHas('organization_team', [
            'team_id' => $team->id,
            'organization_id' => $organization_2->id,
        ]);
    }
}
