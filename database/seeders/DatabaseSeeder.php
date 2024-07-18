<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(DiscussionSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(UserSeeder::class);
    }
}
