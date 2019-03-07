<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment('production')) {
            exit('Database seeders should not be run on production!');
        }

        DB::beginTransaction();

        $this->call(RolesTableSeeder::class);
        $this->call(OptionsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(SubscriptionOptionsSeeder::class);
        $this->call(SubscriptionPlansSeeder::class);

        if (app()->environment('local') || app()->environment('staging')) {
            $this->call(UserSeeder::class);
            $this->call(OrganizationSeeder::class);
            $this->call(TeamsTableSeeder::class);
            $this->call(ProjectsTableSeeder::class);
            $this->call(NodesSeeder::class);
            $this->call(MemosTableSeeder::class);
        }

        DB::commit();
    }
}
