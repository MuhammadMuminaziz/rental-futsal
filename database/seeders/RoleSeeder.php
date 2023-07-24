<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Owner',
            'guard' => 'owner'
        ]);

        $role = array('Super Admin', 'Admin', 'User');
        foreach ($role as $n) {
            Role::create([
                'name' => $n,
                'guard' => 'user'
            ]);
        }
    }
}
