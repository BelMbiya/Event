<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Liens & ciblage
            if (!Schema::hasColumn('contents', 'event_id')) {
                $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('contents', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('contents', 'unique_code')) {
                $table->string('unique_code', 64)->nullable()->index();
            }
            if (!Schema::hasColumn('contents', 'slug')) {
                $table->string('slug')->nullable()->index(); // ex: "benie-gloire-2025"
            }
            if (!Schema::hasColumn('contents', 'locale')) {
                $table->string('locale', 10)->default('fr');
            }

            // Données affichées principales
            if (!Schema::hasColumn('contents', 'couple')) {
                $table->string('couple', 191)->nullable(); // "Bénie & Gloire"
            }
            if (!Schema::hasColumn('contents', 'event_datetime')) {
                $table->dateTime('event_datetime')->nullable(); // snapshot optionnel
            }
            if (!Schema::hasColumn('contents', 'timezone')) {
                $table->string('timezone', 50)->default('Africa/Kinshasa');
            }

            // Lieu & carte
            if (!Schema::hasColumn('contents', 'venue_name')) {
                $table->string('venue_name')->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_address_line1')) {
                $table->string('venue_address_line1')->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_address_line2')) {
                $table->string('venue_address_line2')->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_city')) {
                $table->string('venue_city')->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_region')) {
                $table->string('venue_region')->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_country')) {
                $table->string('venue_country')->nullable()->default('RDC');
            }
            if (!Schema::hasColumn('contents', 'google_maps_url')) {
                $table->string('google_maps_url')->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_lat')) {
                $table->decimal('venue_lat', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('contents', 'venue_lng')) {
                $table->decimal('venue_lng', 10, 7)->nullable();
            }

            // Héros & médias
            if (!Schema::hasColumn('contents', 'hero_image_path')) {
                $table->string('hero_image_path')->nullable(); // ex: storage path de "wed_2.jpg"
            }
            if (!Schema::hasColumn('contents', 'hero_image_alt')) {
                $table->string('hero_image_alt')->nullable();
            }
            if (!Schema::hasColumn('contents', 'gallery')) {
                $table->json('gallery')->nullable(); // ["img1.jpg","img2.jpg",...]
            }

            // Texte & sections
            if (!Schema::hasColumn('contents', 'body_html')) {
                $table->longText('body_html')->nullable(); // remplace {{!! $content->Content !!}}
            }
            if (!Schema::hasColumn('contents', 'intro_1')) {
                $table->text('intro_1')->nullable(); // "C'est avec une immense joie..."
            }
            if (!Schema::hasColumn('contents', 'intro_2')) {
                $table->text('intro_2')->nullable(); // "Rejoignez-nous pour partager..."
            }
            if (!Schema::hasColumn('contents', 'schedule')) {
                $table->json('schedule')->nullable(); // { "ceremony":"15:00", "reception":"18:00" }
            }

            // Thème & mise en forme
            if (!Schema::hasColumn('contents', 'theme')) {
                $table->json('theme')->nullable(); 
                // {
                //   "colors":{"primary":"#e11d48","secondary":"#f43f5e","accent":"#fb7185"},
                //   "fonts":{"headings":"Alex Brush","body":"Cormorant Garamond"},
                //   "decorations":{"floral":true,"overlay":true,"parallax":false}
                // }
            }

            // CTAs
            if (!Schema::hasColumn('contents', 'cta')) {
                $table->json('cta')->nullable();
                // {
                //   "rsvp":{"enabled":true,"label":"Confirmer ma présence 💌","route_name":"guests.rsvp"},
                //   "map":{"enabled":true,"label":"Voir sur la carte 📍"},
                //   "download":{"enabled":true,"label":"Télécharger l'invitation (PDF)"}
                // }
            }

            // Livre d'or
            if (!Schema::hasColumn('contents', 'guestbook_enabled')) {
                $table->boolean('guestbook_enabled')->default(true);
            }
            if (!Schema::hasColumn('contents', 'guestbook_title')) {
                $table->string('guestbook_title')->nullable(); // "Livre d'or"
            }
            if (!Schema::hasColumn('contents', 'guestbook_subtitle')) {
                $table->string('guestbook_subtitle')->nullable(); // "Laissez-nous un mot..."
            }

            // Boissons (libellés/activation; les items viennent d’ailleurs: event_drinks)
            if (!Schema::hasColumn('contents', 'drinks_enabled')) {
                $table->boolean('drinks_enabled')->default(true);
            }
            if (!Schema::hasColumn('contents', 'drinks')) {
                $table->json('drinks')->nullable();
                // {"title":"Choisissez vos boissons","submit_label":"Valider mes choix"}
            }

            // État & publication
            if (!Schema::hasColumn('contents', 'status')) {
                $table->enum('status', ['draft','published','archived'])->default('draft');
            }
            if (!Schema::hasColumn('contents', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }

            // SEO / partage
            if (!Schema::hasColumn('contents', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (!Schema::hasColumn('contents', 'meta_description')) {
                $table->string('meta_description', 300)->nullable();
            }
            if (!Schema::hasColumn('contents', 'og_image')) {
                $table->string('og_image')->nullable();
            }

            // Versioning / audit
            if (!Schema::hasColumn('contents', 'version')) {
                $table->unsignedInteger('version')->default(1);
            }
            if (!Schema::hasColumn('contents', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->index(); // duplication/clonage
            }
            if (!Schema::hasColumn('contents', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->index();
            }
            if (!Schema::hasColumn('contents', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->index();
            }

            // Constraints & indexes utiles
            $table->unique(['event_id','guest_id'], 'contents_event_guest_unique'); // 1 contenu/personnalisation par invité
            if (!Schema::hasColumn('contents', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

public function down(): void
{
    Schema::table('contents', function (Blueprint $table) {
        // 1️⃣ Supprimer les clés étrangères avant les colonnes
        $foreignKeys = ['event_id', 'guest_id', 'invitation_id'];
        foreach ($foreignKeys as $fk) {
            $name = 'contents_' . $fk . '_foreign';
            if ($this->foreignKeyExists('contents', $name)) {
                $table->dropForeign($name);
            }
        }

        // 2️⃣ Supprimer les colonnes
        $cols = [
            'event_id','guest_id','unique_code','slug','locale','couple','event_datetime','timezone',
            'venue_name','venue_address_line1','venue_address_line2','venue_city','venue_region','venue_country',
            'google_maps_url','venue_lat','venue_lng','hero_image_path','hero_image_alt','gallery','body_html',
            'intro_1','intro_2','schedule','theme','cta','guestbook_enabled','guestbook_title','guestbook_subtitle',
            'drinks_enabled','drinks','status','published_at','meta_title','meta_description','og_image',
            'version','parent_id','created_by','updated_by','deleted_at','invitation_id'
        ];
        foreach ($cols as $col) {
            if (Schema::hasColumn('contents', $col)) {
                $table->dropColumn($col);
            }
        }

        // 3️⃣ Supprimer l’index unique s’il existe
        if ($this->indexExists('contents', 'contents_event_guest_unique')) {
            $table->dropUnique('contents_event_guest_unique');
        }
    });
}

/**
 * Vérifie si un index existe
 */
protected function indexExists(string $table, string $indexName): bool
{
    $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
    return count($indexes) > 0;
}

/**
 * Vérifie si une foreign key existe
 */
protected function foreignKeyExists(string $table, string $fkName): bool
{
    $fks = DB::select("SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = ? 
                        AND CONSTRAINT_NAME = ?", [$table, $fkName]);
    return count($fks) > 0;
}

};
