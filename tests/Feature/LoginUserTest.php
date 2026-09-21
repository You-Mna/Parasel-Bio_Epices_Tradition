<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_se_connecter()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/login', [
                'email' => 'test@test.com',
                'password' => 'password123',
                '_token' => 'test-token',
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(302);
    }

    public function test_la_connexion_echoue_avec_un_mauvais_mot_de_passe()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('motdepasse-securise'),
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/login', [
                'email' => 'user@example.com',
                'password' => 'mauvais-motdepasse',
                '_token' => 'test-token',
            ]);

        $this->assertGuest();
        $response->assertStatus(302);
    }
}