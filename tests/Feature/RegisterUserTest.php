<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_creer_un_compte()
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/register', [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean@test.com',
                'phone' => '97000000',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                '_token' => 'test-token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jean@test.com',
        ]);

        $response->assertStatus(302);
    }
}