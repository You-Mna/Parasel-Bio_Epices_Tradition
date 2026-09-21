<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_peut_acceder_au_tableau_de_bord()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_un_client_ne_peut_pas_acceder_au_tableau_de_bord()
    {
        $client = User::factory()->create([
            'role' => 'client',
        ]);

        $response = $this->actingAs($client)->get('/admin');

        $response->assertStatus(403);
    }
}

