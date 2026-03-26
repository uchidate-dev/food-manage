<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // 重複防止キー
            [
                'name' => '管理者',
                'password' => Hash::make('password123'),
                'role' => 1,       // 1 = 管理者
                'mail_flg' => 0,
            ]
        );
    }
}