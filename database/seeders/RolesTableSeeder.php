<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator role'],
            ['name' => 'user', 'description' => 'Regular user role'],
        ];

        Role::insert($roles);
    }
}
