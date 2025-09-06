<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Drink;
use Illuminate\Support\Facades\DB;

class CongoleseDrinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drinks = [
            // Boissons alcoolisées traditionnelles
            ['name' => 'Palm Wine (Vin de Palme)', 'type' => 'wine', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Kasiksi', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 500],
            ['name' => 'Malafu', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
            ['name' => 'Lotoko', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 30],
            ['name' => 'Munkoyo', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 500],
            
            // Bières commerciales locales
            ['name' => 'Primus', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Tembo', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Mützig', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Simba', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Ngok', 'type' => 'beer', 'alcoholic' => true, 'unit' => 'bottle', 'volume_ml' => 330],
            
            // Vins et spiritueux
            ['name' => 'Vinho do Porto', 'type' => 'wine', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 150],
            ['name' => 'Whisky Johnnie Walker', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
            ['name' => 'Vodka Smirnoff', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
            ['name' => 'Rhum Bacardi', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
            ['name' => 'Cognac Hennessy', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
            
            // Boissons non-alcoolisées traditionnelles
            ['name' => 'Bissap (Hibiscus)', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Gingembre', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Tamarindo', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Mangue', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Ananas', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Papaye', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Goyave', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Citron', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Orange', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            ['name' => 'Passion', 'type' => 'juice', 'alcoholic' => false, 'unit' => 'glass', 'volume_ml' => 250],
            
            // Sodas et boissons gazeuses
            ['name' => 'Coca-Cola', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Fanta Orange', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Sprite', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Pepsi', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => 'Mirinda', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 330],
            ['name' => '7UP', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 330],
            
            // Eaux et boissons hydratantes
            ['name' => 'Eau Minérale (1L)', 'type' => 'water', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 1000],
            ['name' => 'Eau Gazeuse', 'type' => 'water', 'alcoholic' => false, 'unit' => 'bottle', 'volume_ml' => 500],
            ['name' => 'Thé Noir', 'type' => 'hot', 'alcoholic' => false, 'unit' => 'cup', 'volume_ml' => 200],
            ['name' => 'Thé Vert', 'type' => 'hot', 'alcoholic' => false, 'unit' => 'cup', 'volume_ml' => 200],
            ['name' => 'Café Congolais', 'type' => 'hot', 'alcoholic' => false, 'unit' => 'cup', 'volume_ml' => 150],
            ['name' => 'Café Robusta', 'type' => 'hot', 'alcoholic' => false, 'unit' => 'cup', 'volume_ml' => 150],
            ['name' => 'Chocolat Chaud', 'type' => 'hot', 'alcoholic' => false, 'unit' => 'cup', 'volume_ml' => 200],
            
            // Boissons énergisantes
            ['name' => 'Red Bull', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'can', 'volume_ml' => 250],
            ['name' => 'Monster', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'can', 'volume_ml' => 500],
            ['name' => 'Burn', 'type' => 'soft', 'alcoholic' => false, 'unit' => 'can', 'volume_ml' => 250],
            
            // Cocktails traditionnels
            ['name' => 'Punch Coco', 'type' => 'cocktail', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 300],
            ['name' => 'Mojito Congolais', 'type' => 'cocktail', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 300],
            ['name' => 'Caipirinha Tropical', 'type' => 'cocktail', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 300],
            ['name' => 'Sangria Africaine', 'type' => 'cocktail', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 300],
            
            // Boissons de luxe
            ['name' => 'Champagne Moët', 'type' => 'wine', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 150],
            ['name' => 'Champagne Dom Pérignon', 'type' => 'wine', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 150],
            ['name' => 'Whisky Macallan', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
            ['name' => 'Cognac Rémy Martin', 'type' => 'spirit', 'alcoholic' => true, 'unit' => 'glass', 'volume_ml' => 50],
        ];

        foreach ($drinks as $drink) {
            Drink::create([
                'name' => $drink['name'],
                'type' => $drink['type'],
                'alcoholic' => $drink['alcoholic'],
                'unit' => $drink['unit'],
                'volume_ml' => $drink['volume_ml'],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeder des boissons congolaises créé avec succès ! ' . count($drinks) . ' boissons ajoutées.');
    }
}