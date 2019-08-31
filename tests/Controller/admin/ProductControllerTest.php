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

    /** @test */
    public function a_guest_cannot_create_a_product()
    {
        $this->client->request('GET', '/admin/products/create');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_create_a_product()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/products/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create Product');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function a_user_can_store_a_product()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/products/create');
        $form = $crawler->selectButton('Save Product')->form();

        $values = [
            'product' => [
                '_token' => $form['product[_token]']->getValue(),
                'title' => 'Test Product',
                'description' => 'Test Product Description',
                'price' => 100,
                'categories' => [1],
                'attributes' => [
                    0 => [
                        'attribute' => 1,
                        'value' => 'Red'
                    ]
                ]
            ]
        ];

        $this->client->request('POST', '/admin/products/create', $values);

        $this->assertResponseRedirects('/admin/products');

        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['title' => 'Test Product']);

        $this->assertNotNull($product);
        $this->assertCount(1, $product->getAttributes());
        $this->assertSame('Test Product Description', $product->getDescription());
    }
}
