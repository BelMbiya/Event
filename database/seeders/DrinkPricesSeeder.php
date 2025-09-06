<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Drink;
use Illuminate\Support\Facades\DB;

class DrinkPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prix réalistes pour les boissons congolaises (en FC)
        $drinkPrices = [
            // Eaux
            'Eau minérale' => 500,
            'Eau de source' => 300,
            'Eau plate' => 200,
            
            // Sodas et boissons gazeuses
            'Coca-Cola' => 1500,
            'Pepsi' => 1500,
            'Fanta' => 1500,
            'Sprite' => 1500,
            'Mirinda' => 1500,
            '7UP' => 1500,
            'Malta Guinness' => 2000,
            'Malta' => 2000,
            
            // Jus
            'Jus d\'orange' => 1000,
            'Jus de pomme' => 1000,
            'Jus de mangue' => 1200,
            'Jus d\'ananas' => 1200,
            'Jus de passion' => 1200,
            'Jus de goyave' => 1200,
            'Jus de papaye' => 1000,
            'Jus de citron' => 800,
            'Jus de gingembre' => 1500,
            'Jus de bissap' => 1000,
            'Jus de tamarin' => 1200,
            'Jus de corossol' => 1500,
            
            // Boissons chaudes
            'Café' => 500,
            'Thé' => 300,
            'Chocolat chaud' => 800,
            'Café au lait' => 700,
            'Thé au lait' => 500,
            'Café noir' => 400,
            'Thé vert' => 400,
            'Café expresso' => 600,
            
            // Bières
            'Primus' => 2500,
            'Tembo' => 2500,
            'Mützig' => 3000,
            'Skol' => 2500,
            'Castel' => 2500,
            '33 Export' => 2500,
            'Heineken' => 3000,
            'Guinness' => 3500,
            'Stella Artois' => 3000,
            'Corona' => 4000,
            
            // Vins
            'Vin rouge' => 8000,
            'Vin blanc' => 8000,
            'Vin rosé' => 8000,
            'Champagne' => 25000,
            'Vin de palme' => 2000,
            
            // Spiritueux
            'Whisky' => 15000,
            'Vodka' => 12000,
            'Rhum' => 10000,
            'Gin' => 12000,
            'Cognac' => 20000,
            'Tequila' => 15000,
            'Liqueur' => 8000,
            
            // Cocktails
            'Cocktail tropical' => 5000,
            'Mojito' => 4000,
            'Piña Colada' => 4500,
            'Daiquiri' => 4000,
            'Margarita' => 4500,
            'Sangria' => 3000,
            
            // Boissons traditionnelles
            'Palm wine' => 2000,
            'Kasiksi' => 1500,
            'Malamba' => 1000,
            'Lotoko' => 800,
            'Nkisi' => 1200,
        ];

        foreach ($drinkPrices as $drinkName => $price) {
            $drink = Drink::where('name', $drinkName)->first();
            if ($drink) {
                // Mettre à jour le prix dans la table event_drinks si la boisson est déjà associée à des événements
                DB::table('event_drinks')
                    ->where('drink_id', $drink->id)
                    ->whereNull('price')
                    ->update(['price' => $price]);
                
                echo "Prix mis à jour pour {$drinkName}: {$price} FC\n";
            }
        }
        
        echo "Seeder des prix des boissons terminé !\n";
    }
}
