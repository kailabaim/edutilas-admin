<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->updateOrCreate(
            ['username' => 'Kaila'],
            [
                'name' => 'Kaila',
                'email' => 'kaila@edutilas.local',
                'username' => 'Kaila',
                'password' => bcrypt('Admin1'),
            ]
        );
    }
}


