<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role' => 'admin'],
            ['role' => 'moderator'],
            ['role' => 'user'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
