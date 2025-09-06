<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invitation_id', 'event_id','guest_id','unique_code','slug','locale','couple','event_datetime','timezone',
        'venue_name','venue_address_line1','venue_address_line2','venue_city','venue_region','venue_country',
        'google_maps_url','venue_lat','venue_lng','hero_image_path','hero_image_alt','gallery','body_html',
        'intro_1','intro_2','schedule','theme','cta','guestbook_enabled','guestbook_title','guestbook_subtitle',
        'drinks_enabled','drinks','status','published_at','meta_title','meta_description','og_image',
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
        'hero_image_alt' => '',
        'guestbook_enabled' => true,
        'guestbook_title' => 'Livre d\'or',
        'guestbook_subtitle' => 'Laissez-nous un message',
        'drinks_enabled' => true,
        'meta_title' => 'Invitation',
        'meta_description' => 'Invitation pour notre événement',
    ];

    /**
     * ✅ CORRECTION : Relations cohérentes
     * Chaque contenu est lié à une invitation spécifique
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
}
