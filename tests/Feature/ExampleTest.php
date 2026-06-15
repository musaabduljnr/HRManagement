<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    public function test_home_page_redirects_to_login()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_login_page_loads()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
