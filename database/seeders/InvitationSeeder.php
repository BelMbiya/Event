<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invitation;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Support\Str;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();
        $guests = Guest::all();

        $invitations = [
            [
                'status' => 'sent',
                'sent_at' => now()->subDays(5),
            ],
            [
                'status' => 'opened',
                'sent_at' => now()->subDays(3),
                'opened_at' => now()->subDays(2),
            ],
            [
                'status' => 'responded',
                'sent_at' => now()->subDays(7),
                'opened_at' => now()->subDays(6),
            ],
            [
                'status' => 'pending',
                'sent_at' => null,
            ],
        ];

        foreach ($events as $event) {
            $eventGuests = $guests->where('event_id', $event->id)->take(4);
            
            foreach ($eventGuests as $index => $guest) {
                if (isset($invitations[$index])) {
                    $invitationData = $invitations[$index];
                    $invitationData['event_id'] = $event->id;
                    $invitationData['guest_id'] = $guest->id;
                    $invitationData['unique_code'] = Str::uuid();
                    $invitationData['invitation_url'] = url('/invitation/' . $invitationData['unique_code']);
                    
                    Invitation::create($invitationData);
                }
            }
        }
    }
}
