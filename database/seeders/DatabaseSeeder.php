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
        $this->call(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'HOA Admin',
            'email' => 'admin@mhv.test',
            'password' => 'admin12345',
            'role_id' => Role::where('name', 'HOA Admin')->value('id'),
        ]);

        $resident = User::factory()->create([
            'name' => 'Demo Resident',
            'email' => 'resident@mhv.test',
            'password' => 'resident12345',
            'role_id' => Role::where('name', 'Resident')->value('id'),
        ]);

        $admin->announcements()->create([
            'title' => 'Welcome to Makati Home Ville',
            'body' => 'This is the official community portal for announcements and complaints.',
        ]);

        $resident->complaints()->create([
            'title' => 'Broken street light',
            'description' => 'The street light near Block 3 has been out for a week.',
        ]);
    }
}
