<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_expedition_en_cours_decremente_le_stock_global()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $pdv = \App\Models\PointDeVente::create([
            'name' => 'Agence test',
            'code' => 'AG-TEST',
            'address' => 'Adresse',
            'phone' => '90000000',
            'email' => 'pdv@example.com',
            'city' => 'Cotonou',
        ]);

        $product = Product::create([
            'name' => 'Produit expédition',
            'slug' => 'produit-expedition',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 50,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $payload = [
            'point_de_vente_id' => $pdv->id,
            'shipped_at' => now()->toDateString(),
            'delivery_method' => null,
            'tracking_reference' => 'REF-TEST',
            'notes' => null,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ];

        $response = $this
            ->actingAs($admin)
            ->post('/admin/shipments', $payload);

        $response->assertRedirect();

        $this->assertDatabaseCount('shipments', 1);
        $shipment = Shipment::first();
        $this->assertEquals(Shipment::STATUS_EN_COURS, $shipment->status);

        $product->refresh();
        $this->assertEquals(45, $product->stock);
    }

    public function test_passage_expedition_en_cours_a_annulee_restore_le_stock()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $pdv = \App\Models\PointDeVente::create([
            'name' => 'Agence test 2',
            'code' => 'AG-TEST-2',
            'address' => 'Adresse',
            'phone' => '91111111',
            'email' => 'pdv2@example.com',
            'city' => 'Cotonou',
        ]);

        $product = Product::create([
            'name' => 'Produit expédition annulée',
            'slug' => 'produit-expedition-annulee',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 30,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $shipment = Shipment::create([
            'point_de_vente_id' => $pdv->id,
            'order_id' => null,
            'status' => Shipment::STATUS_EN_COURS,
            'shipped_at' => now(),
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        // Simuler que le stock a déjà été réservé
        $product->decrement('stock', 10);

        $response = $this
            ->actingAs($admin)
            ->put('/admin/shipments/'.$shipment->id, [
                'point_de_vente_id' => $pdv->id,
                'status' => Shipment::STATUS_ANNULEE,
                'shipped_at' => now()->toDateString(),
                'delivery_method' => null,
                'tracking_reference' => 'REF-ANNUL',
                'notes' => null,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 10],
                ],
            ]);

        $response->assertRedirect();

        $product->refresh();
        $this->assertEquals(30, $product->stock);
    }

    public function test_expedition_marinade_decremente_le_stock_de_la_variante()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $pdv = \App\Models\PointDeVente::create([
            'name' => 'PDV Marinade',
            'code' => 'AG-MAR',
            'address' => 'Adresse',
            'phone' => '92222222',
            'email' => 'pdv-mar@example.com',
            'city' => 'Cotonou',
        ]);

        $product = Product::create([
            'name' => 'Parasel-Bio Marinade',
            'slug' => 'parasel-bio-marinade-test',
            'description' => 'Test variantes',
            'image' => null,
            'price' => 1000,
            'variants' => [
                ['size' => '115g', 'price' => 1200, 'stock' => 5],
                ['size' => '275g', 'price' => 2000, 'stock' => 8],
            ],
            'stock' => 13,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $payload = [
            'point_de_vente_id' => $pdv->id,
            'shipped_at' => now()->toDateString(),
            'delivery_method' => null,
            'tracking_reference' => 'REF-MAR',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'variant_size' => '115g',
                ],
            ],
        ];

        $response = $this
            ->actingAs($admin)
            ->post('/admin/shipments', $payload);

        $response->assertRedirect();

        $product->refresh();
        $variants = $product->variants;

        $this->assertEquals(3, $variants[0]['stock']); // 5 - 2
        $this->assertEquals(8, $variants[1]['stock']); // inchangé
        $this->assertEquals(11, $product->stock);      // 3 + 8
    }

    public function test_modification_expedition_en_cours_met_a_jour_le_stock_selon_la_difference()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $pdv = \App\Models\PointDeVente::create([
            'name' => 'PDV Diff',
            'code' => 'AG-DIFF',
            'address' => 'Adresse',
            'phone' => '93333333',
            'email' => 'pdv-diff@example.com',
            'city' => 'Cotonou',
        ]);

        // Marinade avec stock déjà réservé partiellement (7 restants sur 10, après une première expédition de 3)
        $product = Product::create([
            'name' => 'Parasel-Bio Marinade',
            'slug' => 'parasel-bio-marinade-diff',
            'description' => 'Test diff',
            'image' => null,
            'price' => 1000,
            'variants' => [
                ['size' => '115g', 'price' => 1200, 'stock' => 7],
            ],
            'stock' => 7,
            'is_featured' => false,
            'status' => 'active',
        ]);

        // Expédition existante en cours avec quantité 3 pour la variante 115g
        $shipment = Shipment::create([
            'point_de_vente_id' => $pdv->id,
            'order_id' => null,
            'status' => Shipment::STATUS_EN_COURS,
            'shipped_at' => now(),
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'variant_size' => '115g',
        ]);

        // On modifie l'expédition pour passer la quantité de 3 à 5 (différence +2)
        $response = $this
            ->actingAs($admin)
            ->put('/admin/shipments/'.$shipment->id, [
                'point_de_vente_id' => $pdv->id,
                'status' => Shipment::STATUS_EN_COURS,
                'shipped_at' => now()->toDateString(),
                'delivery_method' => null,
                'tracking_reference' => 'REF-DIFF',
                'notes' => null,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5,
                        'variant_size' => '115g',
                    ],
                ],
            ]);

        $response->assertRedirect();

        $product->refresh();
        $variants = $product->variants;

        // On avait 7 en stock, on réserve 2 supplémentaires => 5 restants
        $this->assertEquals(5, $variants[0]['stock']);
        $this->assertEquals(5, $product->stock);
    }
}

