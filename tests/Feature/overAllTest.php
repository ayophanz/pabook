<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class overAllTest extends TestCase
{
    /**
     * Landing Page Test.
     *
     * @return void
     */
    public function testLandingPage()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    /**
     * Login page test.
     *
     * @return void
     */
    public function testLoginPage()
    {
        $response = $this->get('/login');

        $response->assertStatus(302);
    }

    /**
     * Hotel List test.
     *
     * @return void
     */
    public function testHotelList()
    {
        $response = $this->get('/api/hotels');

        $response->assertStatus(200);
    }
}
