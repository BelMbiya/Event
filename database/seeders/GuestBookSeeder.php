<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuestBook;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;

class GuestBookSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();
        $guests = Guest::all();
        $invitations = Invitation::all();

        $messages = [
            "Félicitations pour ce beau mariage ! Que votre union soit remplie de bonheur et d'amour. 🎉",
            "C'était une magnifique cérémonie ! Merci de nous avoir invités à partager ce moment si spécial.",
            "Que cette nouvelle étape de votre vie soit remplie de joie et de réussite. Tous nos vœux !",
            "Un grand merci pour cette belle fête. Nous avons passé un moment merveilleux en votre compagnie.",
            "Félicitations ! Que votre amour continue de grandir chaque jour. Bonheur éternel ! 💕",
            "Merci pour cette invitation. C'était un plaisir de célébrer avec vous ce jour si important.",
            "Que votre mariage soit le début d'une vie pleine de bonheur et de réussite. Félicitations !",
            "Un moment inoubliable ! Merci de nous avoir permis de partager votre joie. Tous nos vœux !",
            "Félicitations pour ce beau jour ! Que votre amour soit éternel et votre bonheur sans fin.",
            "Merci pour cette magnifique célébration. Nous vous souhaitons tout le bonheur du monde !",
            "Que cette union soit bénie et que votre amour grandisse chaque jour. Félicitations !",
            "Un grand merci pour cette belle invitation. Nous avons passé un moment merveilleux !",
            "Félicitations pour ce mariage ! Que votre vie commune soit remplie de joie et de bonheur.",
            "Merci de nous avoir invités à ce moment si spécial. Tous nos vœux de bonheur !",
            "Que votre amour soit éternel et votre bonheur sans fin. Félicitations pour ce beau mariage !",
        ];

        foreach ($events as $event) {
            $eventGuests = $guests->where('event_id', $event->id)->take(10);
            
            foreach ($eventGuests as $guest) {
                $invitation = $invitations->where('event_id', $event->id)
                                        ->where('guest_id', $guest->id)
                                        ->first();

                $statuses = ['approved', 'pending', 'approved', 'approved', 'pending'];
                $status = $statuses[array_rand($statuses)];

                GuestBook::create([
                    'event_id' => $event->id,
                    'guest_id' => $guest->id,
                    'message' => $messages[array_rand($messages)],
                    'visibility' => $status === 'approved' ? 'public' : 'private',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
