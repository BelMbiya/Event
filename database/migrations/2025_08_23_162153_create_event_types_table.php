<?php

/**
 * ========================================
 * MIGRATION - CRÉATION DE LA TABLE EVENT TYPES
 * ========================================
 * 
 * Cette migration crée la table des types d'événements.
 * Elle définit les catégories d'événements disponibles (mariage, anniversaire, etc.)
 * pour organiser et personnaliser les événements selon leur type.
 */
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
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
