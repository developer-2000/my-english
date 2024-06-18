<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [ 'name' => 'admin', 'description' => 'Administrator role' ],
            [ 'name' => 'user', 'description' => 'Regular user role' ],
        ];

        Role::insert($roles);
    }

}
