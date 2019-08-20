<?php

namespace App\Tests\Controller\Admin;

use App\Tests\DbWebTestCase;

class ProductControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_index_products()
    {
        $this->client->request('GET', '/admin/products');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function index_products()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
        $this->assertCount(2, $crawler->filter('tbody > tr'));
    }
}
