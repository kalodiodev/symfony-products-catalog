<?php

namespace App\Tests\Controller;

use App\Tests\DbWebTestCase;

class ProductControllerTest extends DbWebTestCase
{
    public function testIndexProducts()
    {
        $crawler = $this->client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
        $this->assertCount(2, $crawler->filter('h3'));
    }
}
