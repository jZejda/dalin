<?php

namespace Tests\Feature\Filament;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response_on_login_page()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }
}
