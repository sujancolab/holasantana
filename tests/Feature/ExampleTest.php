<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->followingRedirects()->get('/');

        $response->assertStatus(200);
        $response->assertSee('Earn a profit on your property easily and safely');
    }

    public function test_the_faq_page_renders_the_order_form(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->followingRedirects()->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Submit your order / query');
        $response->assertSee('faq-order-form');
    }
}
