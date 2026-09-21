<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOfflineOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_commande_hors_ligne_cree_ou_reutilise_client_et_decremente_stock()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $product = Product::create([
            'name' => 'Produit hors ligne',
            'slug' => 'produit-hors-ligne',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 20,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $payload = [
            'client_type' => 'new',
            'client_name' => 'Client Hors Ligne',
            'client_phone' => '99001122',
            'client_email' => 'offline@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // On vise la route d’admin pour la création de commande (hors ligne)
        $response = $this
            ->actingAs($admin)
            ->post('/admin/orders', $payload);

        $response->assertRedirect(); // création réussie

        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();

        // Le client doit exister et être relié à la commande
        $this->assertNotNull($order->user_id);
        $this->assertDatabaseHas('users', [
            'id' => $order->user_id,
            'phone' => '99001122',
        ]);

        // Statut attendu pour les commandes hors ligne en back-office
        $this->assertEquals('livree_payee', $order->status);

        // Le stock du produit doit avoir diminué
        $product->refresh();
        $this->assertEquals(17, $product->stock);
    }

    public function test_commande_hors_ligne_reutilise_un_client_existant_par_telephone_et_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Client réel déjà présent dans la base (page Relation clients)
        $existingClient = User::create([
            'name' => 'Client Existant',
            'first_name' => 'Client',
            'last_name' => 'Existant',
            'email' => 'client@example.com',
            'phone' => '99001122',
            'role' => 'client',
            'password' => bcrypt('secret'),
        ]);

        $product = Product::create([
            'name' => 'Produit hors ligne 2',
            'slug' => 'produit-hors-ligne-2',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 5,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $payload = [
            'client_name' => 'Client Existant',
            'client_phone' => '99001122',
            'client_email' => 'client@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this
            ->actingAs($admin)
            ->post('/admin/orders', $payload);

        $response->assertRedirect();

        $order = Order::first();
        $this->assertNotNull($order);

        // On doit avoir réutilisé le même user_id, pas créé un nouveau compte
        $this->assertEquals($existingClient->id, $order->user_id);
        $this->assertEquals(1, User::where('phone', '99001122')->where('email', 'client@example.com')->count());
    }

    public function test_commande_hors_ligne_echoue_si_stock_insuffisant()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $product = Product::create([
            'name' => 'Produit stock limite',
            'slug' => 'produit-stock-limite',
            'description' => 'Test',
            'image' => null,
            'price' => 1000,
            'variants' => null,
            'stock' => 1,
            'is_featured' => false,
            'status' => 'active',
        ]);

        $payload = [
            'client_name' => 'Client Test',
            'client_phone' => '99002233',
            'client_email' => 'test-stock@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5, // plus que le stock disponible
                ],
            ],
        ];

        $response = $this
            ->actingAs($admin)
            ->post('/admin/orders', $payload);

        // On doit être redirigé avec un message d'erreur dans la session
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Aucune commande ne doit avoir été créée et le stock reste inchangé
        $this->assertDatabaseCount('orders', 0);
        $product->refresh();
        $this->assertEquals(1, $product->stock);
    }
}

