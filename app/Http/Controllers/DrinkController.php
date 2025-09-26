<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ========================================
 * DRINK CONTROLLER - GESTION DES BOISSONS
 * ========================================
 * 
 * Ce contrôleur gère le catalogue des boissons disponibles.
 * Il permet la création, modification et suppression des boissons
 * qui seront ensuite utilisées dans les événements.
 */

class DrinkController extends Controller
{
    /**
     * Afficher la liste des boissons
     */
    public function index()
    {
        $drinks = Drink::orderBy('type')->orderBy('name')->get();
        return view('drinks.index', compact('drinks'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('drinks.create');
    }

    /**
     * Créer une nouvelle boisson
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120|unique:drinks,name',
            'type' => 'required|in:water,soft,juice,hot,beer,wine,spirit,cocktail,other',
            'alcoholic' => 'required|boolean',
            'unit' => 'required|in:glass,bottle,can,cup,other',
            'volume_ml' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);

        try {
            Drink::create([
                'name' => $request->name,
                'type' => $request->type,
                'alcoholic' => $request->alcoholic,
                'unit' => $request->unit,
                'volume_ml' => $request->volume_ml,
                'active' => true,
            ]);

            return redirect()->back()
                ->with('success', "Boisson '{$request->name}' créée avec succès !");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la boisson: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $drink = Drink::findOrFail($id);
        return view('drinks.edit', compact('drink'));
    }

    /**
     * Mettre à jour une boisson
     */
    public function update(Request $request, $id)
    {
        $drink = Drink::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:120|unique:drinks,name,' . $id,
            'type' => 'required|in:water,soft,juice,hot,beer,wine,spirit,cocktail,other',
            'alcoholic' => 'required|boolean',
            'unit' => 'required|in:glass,bottle,can,cup,other',
            'volume_ml' => 'nullable|integer|min:1',
            'active' => 'required|boolean',
        ]);

        try {
            $drink->update($request->all());

            return redirect()->back()
                ->with('success', "Boisson '{$drink->name}' mise à jour avec succès !");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une boisson
     */
    public function destroy($id)
    {
        try {
            $drink = Drink::findOrFail($id);
            $drinkName = $drink->name;
            $drink->delete();

            return redirect()->back()
                ->with('success', "Boisson '{$drinkName}' supprimée avec succès !");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
