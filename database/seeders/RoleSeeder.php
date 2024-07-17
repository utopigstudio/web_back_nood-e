<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'id' => 1,
            'Admin' => 'Super Admin',
            'User' => 'Standard User'
        ]);

        Role::create([
            'id' => 2,
            'Admin' => 'Admin',
            'User' => 'User'
        ]);

        Role::create([
            'id' => 3,
            'Admin' => 'Manager',
            'User' => 'Guest User'
        ]);
    }
}
