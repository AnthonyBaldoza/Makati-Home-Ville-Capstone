<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);

    User::factory()->create([
        'name' => 'HOA Admin',
        'email' => 'admin@mhv.test',
        'role_id' => Role::where('name', 'HOA Admin')->value('id'),
    ]);
    }
}
