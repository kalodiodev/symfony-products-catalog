<?php

namespace App\Tests\Controller;

use App\Tests\DbWebTestCase;

class ProductControllerTest extends DbWebTestCase
{
    /** @test */
    public function index_products()
    {
        $crawler = $this->client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
        $this->assertCount(2, $crawler->filter('h3'));
    }

    /** @test */
    public function show_product()
    {
        $this->client->request('GET', '/products/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Product');
        $this->assertStringContainsString('Product 1', $this->client->getResponse()->getContent());
    }
}
