<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $data = [
            [
                'name' => 'admin User',
                'email' => 'dev@allan.com',
                'password'   => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'role' => 'admin'
            ],
            [

                'name' => 'client User',
                'email' => 'dev@client.com',
                'password'   => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'role' => 'client'
            ]
        ];
        User::factory()->createMany($data);
    }
}
