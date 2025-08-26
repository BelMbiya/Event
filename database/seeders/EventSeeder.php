<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizerId = 1;
        $eventTypeId = DB::table('event_types')->where('label', 'Mariage')->value('id');

        DB::table('events')->insert([
            'organizer_id'   => $organizerId,
            'event_type_id'  => $eventTypeId,
            'title'          => 'Mariage de Jean et Marie',
            'description'    => 'Célébration de mariage à Kinshasa avec la famille et les amis.',
            'event_date'     => '2025-12-20 15:00:00',
            'location'       => 'Hôtel Memling, Kinshasa',
            'google_maps_url' => 'https://maps.google.com/?q=Kinshasa+Hotel+Memling',
            'program'        => 'Cérémonie - Cocktail - Dîner - Danse',
            'theme_color'    => '#ff4081',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
