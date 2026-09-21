<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentOnlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_une_commande_payee_en_ligne_est_visible_pour_le_client()
    {
        $user = User::factory()->create();

        // Commande payée en ligne (validée via FedaPay)
        Order::create([
            'user_id' => $user->id,
            'status' => 'payee_en_ligne',
            'total' => 1000,
            'payment_method' => 'fedapay',
            'payment_reference' => 'TX123',
        ]);

        // Commande en attente / en cours de paiement (ne doit pas être visible)
        Order::create([
            'user_id' => $user->id,
            'status' => 'en_cours',
            'total' => 500,
            'payment_method' => 'fedapay',
            'payment_reference' => 'PENDING',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mes-commandes');

        $response->assertStatus(200);

        $orders = $response->viewData('orders');
        $this->assertCount(1, $orders);
        $this->assertEquals('payee_en_ligne', $orders->first()->status);
    }
}

