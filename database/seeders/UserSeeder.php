<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Admin Users
             [
                'name' => 'Shafqat',
                'last_name' => 'Bhatti',
                'display_name' => 'Shafqat Bhatti',
                'email' => 'info@fitwnata.com',
                'password' => Hash::make('loader123'),
                'phone' => '+1234567890',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'admin'
            ],
            [
                'name' => 'John',
                'last_name' => 'Admin',
                'display_name' => 'John Admin',
                'email' => 'admin@fitwnata.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567890',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'admin'
            ],

            // Coach Users
            [
                'name' => 'Mike',
                'last_name' => 'Johnson',
                'display_name' => 'Coach Mike',
                'email' => 'mike.coach@fitwnata.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567891',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'coach'
            ],
            [
                'name' => 'Sarah',
                'last_name' => 'Thompson',
                'display_name' => 'Coach Sarah',
                'email' => 'sarah.coach@fitwnata.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567892',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'coach'
            ],
            [
                'name' => 'David',
                'last_name' => 'Martinez',
                'display_name' => 'Coach David',
                'email' => 'david.coach@fitwnata.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567893',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'coach'
            ],

            // Nutritionist Users
            [
                'name' => 'Emily',
                'last_name' => 'Davis',
                'display_name' => 'Dr. Emily Davis',
                'email' => 'emily.nutritionist@fitwnata.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567894',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'nutritionist'
            ],
            [
                'name' => 'Robert',
                'last_name' => 'Wilson',
                'display_name' => 'Dr. Robert Wilson',
                'email' => 'robert.nutritionist@fitwnata.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567895',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'nutritionist'
            ],

            // Customer Users
            [
                'name' => 'Alice',
                'last_name' => 'Brown',
                'display_name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567896',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Bob',
                'last_name' => 'Smith',
                'display_name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567897',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Carol',
                'last_name' => 'Johnson',
                'display_name' => 'Carol Johnson',
                'email' => 'carol@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567898',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Daniel',
                'last_name' => 'Lee',
                'display_name' => 'Daniel Lee',
                'email' => 'daniel@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567899',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Eva',
                'last_name' => 'Garcia',
                'display_name' => 'Eva Garcia',
                'email' => 'eva@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567800',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Frank',
                'last_name' => 'Miller',
                'display_name' => 'Frank Miller',
                'email' => 'frank@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567801',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Grace',
                'last_name' => 'Anderson',
                'display_name' => 'Grace Anderson',
                'email' => 'grace@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567802',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Henry',
                'last_name' => 'Taylor',
                'display_name' => 'Henry Taylor',
                'email' => 'henry@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567803',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Isabella',
                'last_name' => 'Thomas',
                'display_name' => 'Isabella Thomas',
                'email' => 'isabella@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567804',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
            [
                'name' => 'Jack',
                'last_name' => 'White',
                'display_name' => 'Jack White',
                'email' => 'jack@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567805',
                'email_verified_at' => now(),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                'role' => 'customer'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role to user
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
