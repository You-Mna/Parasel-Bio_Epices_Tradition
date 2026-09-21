<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_stock_du_produit_simple_diminue_apres_une_commande()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Produit stock',
            'slug' => 'produit-stock',
            'description' => 'Description',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 10,
            'is_featured' => false,
            'status' => 'active',
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'variant_price' => 1000,
            'variant_size' => '',
            'cart_key' => $product->id . '_1000_',
        ]);

        $response = $this->actingAs($user)->postJson('/commande', [
            'payment_method' => 'cash',
            'delivery_address' => 'Adresse test',
            'notes' => null,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $product->refresh();
        $this->assertEquals(7, $product->stock);
    }

    public function test_le_stock_de_la_variante_diminue_apres_une_commande()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Produit variantes',
            'slug' => 'produit-variantes',
            'description' => 'Description',
            'image' => null,
            'price' => 1000,
            'variants' => [
                ['size' => '115g', 'price' => 1200, 'stock' => 5],
                ['size' => '275g', 'price' => 2000, 'stock' => 8],
            ],
            'stock' => 0,
            'is_featured' => false,
            'status' => 'active',
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'variant_price' => 1200,
            'variant_size' => '115g',
            'cart_key' => $product->id . '_1200_115g',
        ]);

        $response = $this->actingAs($user)->postJson('/commande', [
            'payment_method' => 'cash',
            'delivery_address' => 'Adresse test',
            'notes' => null,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $product->refresh();
        $variants = $product->variants;

        $this->assertEquals(3, $variants[0]['stock']); // 5 - 2
        $this->assertEquals(8, $variants[1]['stock']); // inchangé
    }
}

