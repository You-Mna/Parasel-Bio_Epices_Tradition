<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_connecte_peut_ajouter_un_produit_au_panier()
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

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post('/panier/ajouter/' . $product->id, [
                '_token' => 'test-token',
                'quantity' => 2,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(302);
    }

    public function test_un_invite_est_redirige_vers_login_lors_de_l_ajout_au_panier()
    {
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

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/panier/ajouter/' . $product->id, [
                '_token' => 'test-token',
                'quantity' => 1,
            ]);

        $response->assertRedirect(route('login.show'));
        $response->assertSessionHas('info');
    }

    public function test_le_total_du_panier_est_correct_pour_un_utilisateur_connecte()
    {
        $user = User::factory()->create();

        $product1 = Product::create([
            'name' => 'Produit 1',
            'slug' => 'produit-1',
            'description' => 'Description',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 10,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $product2 = Product::create([
            'name' => 'Produit 2',
            'slug' => 'produit-2',
            'description' => 'Description',
            'image' => null,
            'price' => 2000,
            'variants' => null,
            'stock' => 5,
            'is_featured' => false,
            'status' => 'active',
        ]);

        // 2 x 1200 + 1 x 2500 = 4900
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'variant_price' => 1200,
            'variant_size' => '',
            'cart_key' => $product1->id . '_1200_',
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'variant_price' => 2500,
            'variant_size' => '',
            'cart_key' => $product2->id . '_2500_',
        ]);

        $response = $this->actingAs($user)->get('/panier');

        $response->assertStatus(200);

        $total = $response->viewData('total');

        // On caste en float pour ignorer le formatage decimal interne
        $this->assertEquals(4900.0, (float) $total);
    }
}

