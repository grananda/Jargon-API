<?php

use App\Models\Role;

class RolesTableSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['roles']);

        $roles = $this->getSeedFileContents('roles');

        foreach ($roles as $role) {
            Role::insert($role);
        }
    }
}
