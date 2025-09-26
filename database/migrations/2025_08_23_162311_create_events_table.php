<?php

/**
 * ========================================
 * MIGRATION - CRÉATION DE LA TABLE EVENTS
 * ========================================
 * 
 * Cette migration crée la table principale des événements.
 * Elle stocke toutes les informations d'un événement : titre, date, lieu,
 * organisateur, type, et options de personnalisation (couleur, programme).
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained('event_types')->onDelete('restrict');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('location');
            $table->string('google_maps_url')->nullable();
            $table->text('program')->nullable();
            $table->string('theme_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
