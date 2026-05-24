<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // Don't forget this import!

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'society_head']);
        Role::create(['name' => 'student']);
    }
}
