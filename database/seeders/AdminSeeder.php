<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::updateOrCreate(
            ['email' => 'info@fitwnata.com'],
            [
                'name' => 'Shafqat Bhatti',
                'email_verified_at' => now(),
                'password' => bcrypt('loader123'), // Always hash passwords
                'remember_token' => Str::random(10),
            ]
        );
    }
}
