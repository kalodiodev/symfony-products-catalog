<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Product;
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

    /** @test */
    public function a_guest_cannot_view_product()
    {
        $this->client->request('GET', '/admin/products/1');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_view_product()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/products/1');

        $product = $this->entityManager->getRepository(Product::class)->find(1);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $product->getTitle());
    }
}
