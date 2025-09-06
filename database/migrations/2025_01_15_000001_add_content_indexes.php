<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier que la table contents existe avant d'ajouter les index
        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                // ✅ AJOUT : Index pour optimiser les performances
                if (!$this->indexExists('contents', 'contents_invitation_id_index')) {
                    $table->index('invitation_id', 'contents_invitation_id_index');
                }
                
                if (!$this->indexExists('contents', 'contents_event_id_index')) {
                    $table->index('event_id', 'contents_event_id_index');
                }
                
                if (!$this->indexExists('contents', 'contents_guest_id_index')) {
                    $table->index('guest_id', 'contents_guest_id_index');
                }
                
                if (!$this->indexExists('contents', 'contents_status_index')) {
                    $table->index('status', 'contents_status_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Supprimer les index
            if ($this->indexExists('contents', 'contents_invitation_id_index')) {
                $table->dropIndex('contents_invitation_id_index');
            }
            
            if ($this->indexExists('contents', 'contents_event_id_index')) {
                $table->dropIndex('contents_event_id_index');
            }
            
            if ($this->indexExists('contents', 'contents_guest_id_index')) {
                $table->dropIndex('contents_guest_id_index');
            }
            
            if ($this->indexExists('contents', 'contents_status_index')) {
                $table->dropIndex('contents_status_index');
            }
        });
    }

    /**
     * Vérifie si un index existe
     */
    protected function indexExists(string $table, string $indexName): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
