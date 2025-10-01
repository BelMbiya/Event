<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ========================================
 * CONTENT MODEL - GESTION DU CONTENU D'INVITATION
 * ========================================
 * 
 * Ce modèle gère le contenu des invitations avec toutes les fonctionnalités :
 * - Contenu personnalisable (textes, images, thèmes)
 * - Gestion des templates et thèmes
 * - Fonctionnalités avancées (RSVP, boissons, livre d'or)
 * - SEO et métadonnées
 * - Synchronisation entre invitations
 * 
 * @author Assistant IA - Optimisation et fusion
 * @version 2.0 - Unifié et optimisé
 */
class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invitation_id', 'event_id','guest_id','unique_code','slug','locale','couple','event_datetime','timezone',
        'venue_name','venue_address_line1','venue_address_line2','venue_city','venue_region','venue_country',
        'google_maps_url','venue_lat','venue_lng','hero_image_path','program_background_image','guestbook_background_image',
        'drinks_background_image','rsvp_background_image','footer_background_image','gallery','body_html','program_html',
        'intro_1','intro_2','schedule','theme','cta','guestbook_enabled','guestbook_title','guestbook_subtitle',
        'drinks_enabled','drinks','status','published_at','meta_title','meta_description','og_image',
        'primary_color','secondary_color','accent_color',
        'version','parent_id','created_by','updated_by',
    ];

    protected $casts = [
        'event_datetime'    => 'datetime',
        'published_at'      => 'datetime',
        'venue_lat'         => 'float',
        'venue_lng'         => 'float',
        'gallery'           => 'array',
        'schedule'          => 'array',
        'theme'             => 'array',
        'cta'               => 'array',
        'drinks'            => 'array',
        'guestbook_enabled' => 'boolean',
        'drinks_enabled'    => 'boolean',
    ];

    protected $attributes = [
        'locale' => 'fr',
        'timezone' => 'Africa/Kinshasa',
        'status' => 'draft',
        'version' => 1,
        'venue_name' => '',
        'venue_address_line1' => '',
        'venue_address_line2' => '',
        'venue_city' => '',
        'venue_region' => '',
        'venue_country' => '',
        'google_maps_url' => '',
        'intro_1' => 'C\'est avec une immense joie que nous vous invitons à célébrer avec nous',
        'intro_2' => 'Nous avons le plaisir de vous inviter à partager ce moment spécial',
        'body_html' => '<p>C\'est avec une immense joie que nous vous invitons à célébrer avec nous ce moment si spécial de notre vie.</p>',
        'guestbook_enabled' => true,
        'guestbook_title' => 'Livre d\'or',
        'guestbook_subtitle' => 'Laissez-nous un message',
        'drinks_enabled' => true,
        'meta_title' => 'Invitation',
        'meta_description' => 'Invitation pour notre événement',
    ];

    /**
     * Relations avec les autres modèles
     */
    public function event() 
    { 
        return $this->belongsTo(Event::class); 
    }
    
    public function guest() 
    { 
        return $this->belongsTo(Guest::class); 
    }
    
    public function invitation() 
    { 
        return $this->belongsTo(Invitation::class, 'invitation_id'); 
    }

    /**
     * Scopes utiles
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Accesseurs utiles
     */
    public function getFullNameAttribute()
    {
        return $this->couple ?? 'Invitation';
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published';
    }

    public function getThemeColorsAttribute()
    {
        $theme = is_string($this->theme) ? json_decode($this->theme, true) : $this->theme;
        return $theme['colors'] ?? [];
    }

    public function getThemeFontsAttribute()
    {
        $theme = is_string($this->theme) ? json_decode($this->theme, true) : $this->theme;
        return $theme['fonts'] ?? [];
    }
}
