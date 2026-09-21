<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLowStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_produit_sous_20_pourcent_du_stock_initial_apparait_en_alerte()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Stock initial 100, stock actuel 15 -> 15% => doit être dans la liste
        Product::create([
            'name' => 'Produit critique',
            'slug' => 'produit-critique',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 15,
            'initial_stock' => 100,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Produit critique');
    }

    public function test_mise_a_jour_du_stock_redefinition_du_stock_initial_met_a_jour_l_alerte()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $product = Product::create([
            'name' => 'Produit ajusté',
            'slug' => 'produit-ajuste',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 10,
            'initial_stock' => 100,
            'is_featured' => false,
            'status' => 'active',
        ]);

        // On simule une mise à jour via le back-office qui met le stock (et initial_stock) à 10
        $this->actingAs($admin)->put('/admin/products/'.$product->id, [
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'stock' => 10,
            'status' => 'active',
        ]);

        $product->refresh();
        $this->assertEquals(10, $product->initial_stock);

        // 10 / 10 = 100% => ne doit plus apparaître dans les alertes
        $response = $this
            ->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertDontSee('Produit ajusté');
    }
}

