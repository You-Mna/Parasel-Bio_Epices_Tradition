<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientOrdersVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_mes_commandes_ne_montre_que_les_commandes_payees_ou_a_la_livraison()
    {
        $user = User::factory()->create();

        // Deux commandes visibles : une payée en ligne, une à la livraison
        $visibleOrders = [
            Order::create([
                'user_id' => $user->id,
                'status' => 'payee_en_ligne',
                'total' => 1000,
                'payment_method' => 'fedapay',
                'payment_reference' => 'OK_ONLINE',
            ]),
            Order::create([
                'user_id' => $user->id,
                'status' => 'a_la_livraison',
                'total' => 1500,
                'payment_method' => 'cash',
                'payment_reference' => 'OK_CASH',
            ]),
        ];

        // Une commande qui ne doit pas apparaître : paiement en ligne encore "en_cours"
        Order::create([
            'user_id' => $user->id,
            'status' => 'en_cours',
            'total' => 800,
            'payment_method' => 'fedapay',
            'payment_reference' => 'PENDING',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mes-commandes');

        $response->assertStatus(200);

        $orders = $response->viewData('orders');
        $this->assertCount(2, $orders);
        $this->assertEqualsCanonicalizing(
            ['payee_en_ligne', 'a_la_livraison'],
            $orders->pluck('status')->all()
        );
    }
}

