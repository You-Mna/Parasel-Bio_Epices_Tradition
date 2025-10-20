<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Experience;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin Parasel Bio',
            'first_name' => 'Admin',
            'last_name' => 'Parasel',
            'email' => 'admin@parasel-bio.tld',
            'phone' => '+221770000000',
            'role' => 'admin',
            'password' => Hash::make('admin1234'),
        ]);

        // Clients
        User::factory(5)->create();

        // Products
        $products = [
            [
                'name' => 'Parasel-Bio Marinade', 
                'slug' => 'parasel-bio-marinade', 
                'price' => 1000, 
                'status' => 'active',
                'variants' => [
                    ['size' => '115g', 'price' => 1000, 'stock' => 50],
                    ['size' => '275g', 'price' => 1500, 'stock' => 30],
                    ['size' => '850g', 'price' => 5000, 'stock' => 20]
                ],
                'stock' => 50, 
                'description' => 'Le sel assaisonné aux 17 épices et légumes naturels bio pour sublimer vos plats.','is_featured' => true
            ],
            [
                'name' => 'Xladjê du Chef Paludier', 
                'slug' => 'xladje-chef-paludier', 
                'price' => 2000, 
                'status' => 'active',
                'variants' => [
                    ['size' => '850g', 'price' => 2000, 'stock' => 0]
                ],
                'stock' => 0, 
                'description' => 'Le sel gris, enrichi de feuilles de moringa pour les personnes diabétiques.','is_featured' => true
            ],
            [
                'name' => 'Arôme Parasel', 
                'slug' => 'arome-parasel', 
                'price' => 1500, 
                'status' => 'active',
                'variants' => [
                    ['size' => '33cl', 'price' => 1500, 'stock' => 25]
                ],
                'stock' => 25, 
                'description' => 'Arôme naturel salé aux 17 épices et légumes de Parasel-bio Marinade.','is_featured' => false
            ],
            [
                'name' => 'ParaStress', 
                'slug' => 'parastress', 
                'price' => 5000, 
                'status' => 'active',
                'variants' => [
                    ['size' => '850g', 'price' => 5000, 'stock' => 0]
                ],
                'stock' => 0, 
                'description' => 'Le sel relaxant pour réduire le stress.', 
                'image' => 'parastress.jpg', 
                'is_featured' => false
            ],
        ];
        foreach ($products as $p) {
            Product::create($p);
        }

        // Experiences
        Experience::create([
            'author_name' => 'Aïcha Traoré',
            'content' => 'Des produits excellents, livrés rapidement. Je recommande !',
            'is_published' => true,
        ]);
        Experience::create([
            'author_name' => 'Mamadou Diallo',
            'content' => 'Très satisfait du service client et de la qualité.',
            'is_published' => true,
        ]);
        Experience::create([
            'author_name' => 'Fatou Sarr',
            'content' => 'Les épices Parasel-Bio ont révolutionné ma cuisine. Un goût authentique incomparable !',
            'is_published' => true,
        ]);
    }
}
