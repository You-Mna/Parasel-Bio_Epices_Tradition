<?php

namespace Tests\Feature;

use Tests\TestCase;

class PointsDistributionPageTest extends TestCase
{
    public function test_la_page_points_de_vente_saffiche(): void
    {
        $response = $this->get('/points-de-vente');

        $response->assertStatus(200);
        $response->assertSee('Points de Distribution', false);
        $response->assertSee('Bénin', false);
    }
}
