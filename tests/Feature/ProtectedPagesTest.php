<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProtectedPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invite_est_redirige_vers_login_pour_mon_compte()
    {
        $response = $this->get('/mon-compte');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_un_invite_est_redirige_vers_login_pour_mes_commandes()
    {
        $response = $this->get('/mes-commandes');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}

