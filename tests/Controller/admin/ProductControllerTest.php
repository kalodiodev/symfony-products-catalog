<?php

namespace App\Tests\Controller\Admin;

use App\Tests\DbWebTestCase;

class ProductControllerTest extends DbWebTestCase
{
    public function testAGuestCannotIndexProducts()
    {
        $this->client->request('GET', '/admin/products');

        $this->assertResponseRedirects('/login');
    }

    public function testIndexProducts()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
        $this->assertCount(3, $crawler->filter('tr'));
    }
}
