<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::factory()->create([
            "id" => RoleEnum::super,
            "name" => "Super"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::admin,
            "name" => "Admin"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::user,
            "name" => "User"
        ]);
    }
}
