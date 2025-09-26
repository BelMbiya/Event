<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * DRINK MODEL - GESTION DES BOISSONS
 * ========================================
 * 
 * Ce modèle représente une boisson disponible dans le système.
 * Il stocke les informations de base (nom, prix, disponibilité)
 * et sert de référence pour les boissons d'événements.
 */

class Drink extends Model
{
    protected $fillable = ['name', 'price', 'available'];
}
