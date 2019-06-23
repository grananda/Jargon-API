<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Faker\Generator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $migrated = false;

    /**
     * @var \App\Models\User
     */
    protected $signedUser;

    /**
     * The Faker Generator instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = app(Generator::class);

        if (! self::$migrated) {
            Artisan::call('migrate:fresh');
            self::$migrated = true;
        }
        DB::beginTransaction();
        Artisan::call('db:seed');
    }

    public function tearDown(): void
    {
        DB::rollback();
        parent::tearDown();
    }

    /**
     * Sign in a specific user or create and sign in a stub.
     *
     * @param \App\Models\User|null $user
     * @param array                 $overrides
     *
     * @return $this
     */
    public function signIn(User $user = null, $overrides = [])
    {
        $this->signedUser = $user ?? factory(User::class)->create($overrides);
        $this->actingAs($this->signedUser, 'api');

        return $this;
    }

    /**
     * Creates a staff user.
     *
     * @param string $role
     * @param array  $overrides
     *
     * @return \App\Models\User
     */
    public function staff(string $role = 'super-admin', array $overrides = [])
    {
        /** @var \App\Models\User $staff */
        $staff = factory(User::class)->create($overrides);
        $staff->setRole(Role::where('alias', $role)->first());

        return $staff;
    }

    /**
     * Creates a non-staff user.
     *
     * @param string $role
     * @param array  $overrides
     *
     * @return \App\Models\User
     */
    public function user(string $role = 'registered-user', array $overrides = [])
    {
        /** @var \App\Models\User $staff */
        $staff = factory(User::class)->create($overrides);
        $staff->setRole(Role::where('alias', $role)->first());

        return $staff;
    }

    /**
     * Log outs user.
     *
     * @return void
     */
    public function signOut(): void
    {
        Auth::logout();
    }

    /**
     * Reads the contents of the given test fixture.
     *
     * @param string $name
     * @param bool   $json
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function loadFixture(string $name, $json = true)
    {
        $file = $json ? __DIR__."/fixtures/{$name}.json" : __DIR__."/fixtures/{$name}";

        if (file_exists($file)) {
            $content = file_get_contents($file);

            return $json ? json_decode($content, true) : $content;
        }

        throw new \Exception('Fixture was not found!');
    }
}
