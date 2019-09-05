<?php

namespace App\Tests\Controller\Admin;

use App\Tests\DbWebTestCase;

class DashboardControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_visit_admin_dashboard()
    {
        $this->client->request('GET', '/admin');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function admin_dashboard()
    {
        $this->logIn();

        $this->client->catchExceptions(false);

        $this->client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }
}