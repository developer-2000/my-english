<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // присвоить админу все уровни доступа
        $userRole = Role::where('name', 'user')->first();
        $adminRole = Role::where('name', 'admin')->first();

        // Создаем пользователя-администратора
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('admin'),
        ]);

        // Присваиваем пользователю роль 'admin'
        $admin->roles()->attach($userRole);
        $admin->roles()->attach($adminRole);
    }
}
