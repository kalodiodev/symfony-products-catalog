<?php

namespace App\Tests\Controller\Admin;

use App\Tests\DbWebTestCase;

class UserControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_index_users()
    {
        $this->client->request('GET', '/admin/users');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function index_users()
    {
        $this->login();

        $this->client->catchExceptions(false);

        $crawler = $this->client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Users');
        $this->assertCount(1, $crawler->filter('tbody > tr'));
    }
}