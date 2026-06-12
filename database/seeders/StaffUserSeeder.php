<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => '佐藤 担当者',
                'email' => 'sato@example.com',
                'role' => 'staff',
            ],
            [
                'name' => '鈴木 担当者',
                'email' => 'suzuki@example.com',
                'role' => 'staff',
            ],
            [
                'name' => '高橋 担当者',
                'email' => 'takahashi@example.com',
                'role' => 'staff',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                ]
            );
        }
    }
}
