<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'first_name' => 'user1',
            'last_name' => 'test',
            'email' => 'user1@example.com',
            'password' => bcrypt('user1@example.com'),
        ]);

        $this->call([
            AdminSeeder::class,
        ]);
    }
}
