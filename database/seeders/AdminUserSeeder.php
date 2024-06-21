<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Создаем пользователя-администратора
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        // Присваиваем пользователю роль 'user'
        $admin->assignRole('user');
        // Присваиваем пользователю роль 'admin'
        $admin->assignRole('admin');
    }
}
