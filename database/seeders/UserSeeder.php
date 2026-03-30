<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ゲストユーザーの作成
        User::updateOrCreate(
            ['email' => 'guest@example.com'], // このメアドでログインさせるよ！
            [
                'name' => 'ゲストユーザー',
                'password' => Hash::make('password123'),
                'role' => 0,       // 0 = 一般ユーザー
                'mail_flg' => 0,
            ]
        );
    }
}
