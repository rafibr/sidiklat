<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@sipandik.local'],
            [
                'name' => 'Administrator SIDIKLAT',
                'password' => Hash::make('password'),
            ]
        );
    }
}
