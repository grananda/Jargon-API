<?php

namespace Tests;

use App\Models\User;
use Faker\Generator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

        if (!self::$migrated) {
            Artisan::call('migrate:fresh');
            self::$migrated = true;
        }
        DB::beginTransaction();
        Artisan::call('db:seed');

        Mail::fake();
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
     * Log outs user.
     *
     * @return void
     */
    public function signOut(): void
    {
        Auth::logout();
    }
}
