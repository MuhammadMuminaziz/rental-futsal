<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Owner::create([
            'role_id' => 1,
            'name' => "Admin Rental Futsal",
            'email' => 'renfut@gmail.com',
            'password' => Hash::make('password')
        ]);
    }
}
