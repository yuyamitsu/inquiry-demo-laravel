<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => '管理者',
                'email' => 'admin@example.com',
            ],
            [
                'name' => '佐藤 担当者',
                'email' => 'sato@example.com',
            ],
            [
                'name' => '鈴木 担当者',
                'email' => 'suzuki@example.com',
            ],
            [
                'name' => '高橋 担当者',
                'email' => 'takahashi@example.com',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                ]
            );
        }
    }
}
