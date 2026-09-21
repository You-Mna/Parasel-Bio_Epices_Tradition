<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_client_connecte_avec_un_panier_vide_est_redirige_vers_le_panier()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/commande');

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Votre panier est vide.');
    }

    public function test_un_client_connecte_avec_un_panier_rempli_peut_acceder_au_checkout()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Produit test',
            'slug' => 'produit-test',
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
            'quantity' => 1,
            'variant_price' => 1000,
            'variant_size' => '',
            'cart_key' => $product->id . '_1000_',
        ]);

        $response = $this->actingAs($user)->get('/commande');

        $response->assertStatus(200);
    }
}

