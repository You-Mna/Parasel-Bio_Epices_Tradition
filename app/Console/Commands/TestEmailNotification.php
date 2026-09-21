<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Console\Command;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi d\'email de notification de commande';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Créer un utilisateur temporaire pour le test
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'phone' => '123456789',
                'password' => bcrypt('password'),
                'role' => 'client'
            ]
        );

        // Créer une commande de test
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'a_la_livraison',
            'total' => 5000,
            'payment_method' => 'mobile_money',
            'payment_reference' => 'TEST123'
        ]);

        // Envoyer la notification
        $user->notify(new OrderStatusUpdated($order, 'a_la_livraison', 'livree_payee'));

        $this->info("Email de test envoyé à {$email}");
    }
}
