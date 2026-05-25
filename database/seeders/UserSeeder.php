<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'admin', 'email' => 'admin@example.com' , 'password'=> bcrypt('12345') , 'role' => 'admin'],
            ['name' => 'HR Manager', 'email' => 'hr@example.com' , 'password'=> bcrypt('12345') , 'role' => 'hr'],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
