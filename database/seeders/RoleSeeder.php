<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'coppa_user', 'guard_name' => 'web']);
        Role::create(['name' => 'registered_user', 'guard_name' => 'web']);
        Role::create(['name' => 'validating_user', 'guard_name' => 'web']);
        Role::create(['name' => 'banned_user', 'guard_name' => 'web']);
        Role::create(['name' => 'suspended_user', 'guard_name' => 'web']);
    }
}
