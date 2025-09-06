<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\GuestDrinkChoice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnhancedDashboardController extends Controller
{
    public function index()
    {
        // Statistiques principales
        $stats = [
            'active_events' => Event::where('event_date', '>=', now())->count(),
            'total_guests' => Guest::count(),
            'sent_invitations' => Invitation::whereNotNull('sent_at')->count(),
            'confirmed_rsvp' => Guest::where('rsvp_status', 'confirmed')->count(),
        ];

        // Événements récents avec statistiques
        $recentEvents = Event::withCount(['invitations', 'guests'])
            ->withCount(['guests as confirmed_rsvp_count' => function($query) {
                $query->where('rsvp_status', 'confirmed');
            }])
            ->withCount(['guests as total_guests_count'])
            ->orderBy('event_date', 'desc')
            ->take(5)
            ->get();

        // Activité récente simulée (dans une vraie app, ce serait des logs)
        $recentActivity = collect([
            [
                'icon' => 'envelope',
                'color' => 'primary',
                'message' => 'Nouvelle invitation créée pour "Mariage de Jean & Marie"',
                'time' => 'Il y a 2 minutes'
            ],
            [
                'icon' => 'check-circle',
                'color' => 'success',
                'message' => 'RSVP confirmé par Pierre Dupont',
                'time' => 'Il y a 15 minutes'
            ],
            [
                'icon' => 'glass-cheers',
                'color' => 'info',
                'message' => 'Choix de boisson ajouté par Sophie Martin',
                'time' => 'Il y a 1 heure'
            ],
            [
                'icon' => 'calendar-plus',
                'color' => 'warning',
                'message' => 'Nouvel événement "Anniversaire" créé',
                'time' => 'Il y a 2 heures'
            ]
        ]);

        return view('dashboard-enhanced', compact('stats', 'recentEvents', 'recentActivity'));
    }

    public function getStats()
    {
        $stats = [
            'active_events' => Event::where('event_date', '>=', now())->count(),
            'total_guests' => Guest::count(),
            'sent_invitations' => Invitation::whereNotNull('sent_at')->count(),
            'confirmed_rsvp' => Guest::where('rsvp_status', 'confirmed')->count(),
            'pending_rsvp' => Guest::where('rsvp_status', 'pending')->count(),
            'declined_rsvp' => Guest::where('rsvp_status', 'declined')->count(),
            'total_drink_choices' => GuestDrinkChoice::count(),
        ];

        return response()->json($stats);
    }

    public function getRecentActivity()
    {
        // Dans une vraie application, vous récupéreriez les vraies activités depuis une table de logs
        $activities = collect([
            [
                'icon' => 'envelope',
                'color' => 'primary',
                'message' => 'Nouvelle invitation créée',
                'time' => Carbon::now()->subMinutes(5)->diffForHumans()
            ],
            [
                'icon' => 'check-circle',
                'color' => 'success',
                'message' => 'RSVP confirmé',
                'time' => Carbon::now()->subMinutes(20)->diffForHumans()
            ],
            [
                'icon' => 'glass-cheers',
                'color' => 'info',
                'message' => 'Choix de boisson ajouté',
                'time' => Carbon::now()->subHour()->diffForHumans()
            ]
        ]);

        return response()->json($activities);
    }
}
