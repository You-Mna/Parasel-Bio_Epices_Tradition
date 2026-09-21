<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccueilTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_accueil_saffiche()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}