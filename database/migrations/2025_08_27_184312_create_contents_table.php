<?php

/**
 * ========================================
 * MIGRATION - CRÉATION DE LA TABLE CONTENTS
 * ========================================
 * 
 * Cette migration crée la table des contenus d'invitations avec toutes les fonctionnalités.
 * Elle stocke les informations complètes des invitations : couple, familles,
 * contenu personnalisé, thèmes, couleurs, images, et toutes les fonctionnalités avancées.
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            
            // Liens & ciblage
            $table->foreignId('invitation_id')->nullable()->constrained('invitations')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('unique_code', 64)->nullable()->index();
            $table->string('slug')->nullable()->index(); // ex: "benie-gloire-2025"
            $table->string('locale', 10)->default('fr');

            // Données affichées principales
            $table->string('couple', 191)->nullable(); // "Bénie & Gloire"
            $table->dateTime('event_datetime')->nullable(); // snapshot optionnel
            $table->string('timezone', 50)->default('Africa/Kinshasa');

            // Lieu & carte
            $table->string('venue_name')->nullable();
            $table->string('venue_address_line1')->nullable();
            $table->string('venue_address_line2')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('venue_region')->nullable();
            $table->string('venue_country')->nullable()->default('RDC');
            $table->string('google_maps_url')->nullable();
            $table->decimal('venue_lat', 10, 7)->nullable();
            $table->decimal('venue_lng', 10, 7)->nullable();

            // Héros & médias
            $table->string('hero_image_path')->nullable(); // ex: storage path de "wed_2.jpg"
            $table->string('program_background_image')->nullable();
            $table->string('guestbook_background_image')->nullable();
            $table->string('drinks_background_image')->nullable();
            $table->string('rsvp_background_image')->nullable();
            $table->string('footer_background_image')->nullable();
            $table->json('gallery')->nullable(); // ["img1.jpg","img2.jpg",...]

            // Texte & sections
            $table->longText('body_html')->nullable(); // contenu principal
            $table->longText('program_html')->nullable(); // Programme avec Summernote
            $table->text('intro_1')->nullable(); // "C'est avec une immense joie..."
            $table->text('intro_2')->nullable(); // "Rejoignez-nous pour partager..."
            $table->json('schedule')->nullable(); // { "ceremony":"15:00", "reception":"18:00" }

            // Thème & mise en forme
            $table->json('theme')->nullable(); 
            // {
            //   "colors":{"primary":"#e11d48","secondary":"#f43f5e","accent":"#fb7185"},
            //   "fonts":{"headings":"Alex Brush","body":"Cormorant Garamond"},
            //   "decorations":{"floral":true,"overlay":true,"parallax":false}
            // }

            // CTAs
            $table->json('cta')->nullable();
            // {
            //   "rsvp":{"enabled":true,"label":"Confirmer ma présence 💌","route_name":"guests.rsvp"},
            //   "map":{"enabled":true,"label":"Voir sur la carte 📍"},
            //   "download":{"enabled":true,"label":"Télécharger l'invitation (PDF)"}
            // }

            // Livre d'or
            $table->boolean('guestbook_enabled')->default(true);
            $table->string('guestbook_title')->nullable(); // "Livre d'or"
            $table->string('guestbook_subtitle')->nullable(); // "Laissez-nous un mot..."

            // Boissons (libellés/activation; les items viennent d'ailleurs: event_drinks)
            $table->boolean('drinks_enabled')->default(true);
            $table->json('drinks')->nullable();
            // {"title":"Choisissez vos boissons","submit_label":"Valider mes choix"}

            // État & publication
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->timestamp('published_at')->nullable();

            // SEO / partage
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('og_image')->nullable();

            // Versioning / audit
            $table->unsignedInteger('version')->default(1);
            $table->unsignedBigInteger('parent_id')->nullable()->index(); // duplication/clonage
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Constraints & indexes utiles
            $table->unique(['event_id','guest_id'], 'contents_event_guest_unique'); // 1 contenu/personnalisation par invité
            
            // Index pour optimiser les performances
            $table->index('invitation_id', 'contents_invitation_id_index');
            $table->index('event_id', 'contents_event_id_index');
            $table->index('guest_id', 'contents_guest_id_index');
            $table->index('status', 'contents_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
