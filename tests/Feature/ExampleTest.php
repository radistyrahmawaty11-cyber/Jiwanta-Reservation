<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Route "/" berada di belakang middleware "auth", jadi tamu harus
     * diarahkan ke halaman login.
     */
    public function test_guests_are_redirected_from_home_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Halaman login harus bisa diakses tanpa autentikasi.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
