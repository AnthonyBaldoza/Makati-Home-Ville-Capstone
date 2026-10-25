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
        $this->call(FacilitySeeder::class);

        $admin = User::factory()->create([ #Fact0ry para mas pabilis ang at consistent and paggawa ng demo account 
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
            'block' => '3',
            'lot' => '12',
            'mobile_number' => '09171234567',
        ]);

        User::factory()->create([
            'name' => 'Facility Staff',
            'email' => 'staff@mhv.test',
            'password' => 'staff12345',
            'role_id' => Role::where('name', 'Facility Staff')->value('id'),
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
