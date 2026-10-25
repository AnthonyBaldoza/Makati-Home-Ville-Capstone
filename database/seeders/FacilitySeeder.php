<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        Facility::firstOrCreate( #Naglalagay ng default facilities sa database.
            ['name' => 'Community House'],
            ['description' => 'Community houses for residents']
        );
        Facility::firstOrCreate(
            ['name' => 'Basketball Court'],
            ['description' => 'Covered court for games and events.']
        );
    }
}
