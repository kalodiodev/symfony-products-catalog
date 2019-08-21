<?php

namespace App\Tests\Controller\Admin;

use App\Tests\DbWebTestCase;

class BrandControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_index_brands()
    {
        $this->client->request('GET', '/admin/brands');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function index_categories()
    {
        $this->logIn();

        $this->client->catchExceptions(false);
        $crawler = $this->client->request('GET', '/admin/brands');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Brands');
        $this->assertCount(3, $crawler->filter('tbody > tr'));
    }
}