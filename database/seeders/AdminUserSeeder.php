<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
        ]);

        //Add roles to admin and superadmin users
        User::find(11)->roles()->attach(2);
        User::find(12)->roles()->attach(3);
    }
}
