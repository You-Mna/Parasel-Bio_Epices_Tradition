<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_la_page_login_saffiche()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}