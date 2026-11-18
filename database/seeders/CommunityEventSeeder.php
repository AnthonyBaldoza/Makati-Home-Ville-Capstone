<?php

namespace Database\Seeders;

use App\Models\CommunityEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunityEventSeeder extends Seeder
{
    public function run(): void
    {
        $uid = User::value('id');

        $events = [
            [
                'title'       => 'Barangay Basketball Tournament',
                'type'        => 'barangay',
                'location'    => 'Barangay Covered Court',
                'description' => 'Friendly tournament for all residents.',
                'start_at'    => '2026-11-19 09:00:00',
                'end_at'      => '2026-11-19 17:00:00',
            ],
            [
                'title'       => 'Community Clean-up Drive',
                'type'        => 'cleanup',
                'location'    => 'Main Street',
                'description' => 'Bring gloves, walis tingting, dustpan at trash bags.',
                'start_at'    => '2026-11-23 07:00:00',
                'end_at'      => '2026-11-23 10:00:00',
            ],
            [
                'title'       => 'Typhoon Safety and Preparedness Seminar',
                'type'        => 'seminar',
                'location'    => 'Community House',
                'description' => 'Paghahanda at kaligtasan tuwing may bagyo.',
                'start_at'    => '2026-11-26 09:00:00',
                'end_at'      => '2026-11-26 12:00:00',
            ],
            [
                'title'       => 'Cash for Work Community Meeting',
                'type'        => 'meeting',
                'location'    => 'Office Center Phase 3',
                'description' => 'Cash for Work Informations',
                'start_at'    => '2026-11-28 13:00:00',
                'end_at'      => '2026-11-28 15:00:00',
            ],
        ];

        foreach ($events as $e) {
            $existing = CommunityEvent::where('title', $e['title'])
                ->where('start_at', $e['start_at'])
                ->first();

            // Laktawan kung may sumasapaw na ibang event
            $conflict = CommunityEvent::findConflict($e['start_at'], $e['end_at'], $existing?->id);

            if ($conflict) {
                $this->command->warn("SKIPPED '{$e['title']}': sumasapaw sa '{$conflict->title}'");
                continue;
            }

            CommunityEvent::updateOrCreate(
                ['title' => $e['title'], 'start_at' => $e['start_at']],
                $e + ['created_by' => $uid]
            );
        }
    }
}