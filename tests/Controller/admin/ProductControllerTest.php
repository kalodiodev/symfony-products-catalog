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

        $values = $this->productFormData();
        $values['product']['_token'] = $form['product[_token]']->getValue();

        $this->client->request('POST', '/admin/products/create', $values);

        $this->assertResponseRedirects('/admin/products');

        $product = $this->entityManager->getRepository(Product::class)
            ->findOneBy(['title' => 'Test Product']);

        $this->assertNotNull($product);
        $this->assertCount(1, $product->getAttributes());
        $this->assertSame('Test Product Description', $product->getDescription());
    }

    /** @test */
    public function a_guest_cannot_edit_a_product()
    {
        $this->client->request('GET', '/admin/products/1/edit');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_edit_a_product()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/products/1/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Update Product');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function a_user_can_update_a_product()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/products/1/edit');
        $form = $crawler->selectButton('Save Product')->form();

        $values = $this->productFormData();
        $values['product']['_token'] = $form['product[_token]']->getValue();

        $this->client->request('POST', '/admin/products/1/edit', $values);

        $this->assertResponseRedirects('/admin/products');

        $product = $this->entityManager->getRepository(Product::class)->find(1);

        $this->assertNotNull($product);
        $this->assertCount(1, $product->getAttributes());
        $this->assertSame('Test Product', $product->getTitle());
        $this->assertSame('Test Product Description', $product->getDescription());
        $this->assertEquals(100, $product->getPrice());

        $attribute = $product->getAttributes()[0];
        $this->assertSame(1, $attribute->getAttribute()->getId());
        $this->assertSame('Red', $attribute->getValue());
    }

    /**
     * @test
     * @dataProvider invalidFormData
     */
    public function store_product_validation($title, $description, $price, $categories, $attributes)
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/products/create');
        $form = $crawler->selectButton('Save Product')->form();

        $values = $this->productFormData();
        $values['product']['_token'] = $form['product[_token]']->getValue();

        $values['product']['title'] = $title;
        $values['product']['description'] = $description;
        $values['product']['price'] = $price;
        $values['product']['categories'] = $categories;
        $values['product']['attributes'] = $attributes;

        $this->client->request('POST', '/admin/products/create', $values);

        $this->assertRouteSame('admin_products_create');

        $product = $this->entityManager->getRepository(Product::class)
            ->findOneBy(['title' => 'Test Product']);

        $this->assertNull($product);
    }

    /** @test */
    public function a_guest_cannot_delete_a_product()
    {
        $this->client->request('POST', '/admin/products/1/delete');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_delete_a_product()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/products/1/edit');
        $this->client->submitForm('Delete Product');

        $product = $this->entityManager->getRepository(Product::class)->find(1);

        $this->assertNull($product);
    }

    /** @test */
    public function cannot_delete_a_product_with_invalid_csrf_token()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/products/1/edit');
        $this->client->submitForm('Delete Product', ['token' => "invalid_token"]);

        $this->assertResponseRedirects('/admin/products/1/edit');

        $product = $this->entityManager->getRepository(Product::class)->find(1);

        $this->assertNotNull($product);
    }

    private function productFormData()
    {
        return array_merge([
            'product' => [
                '_token' => '',
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
        ]);
    }

    public function invalidFormData()
    {
        yield ['', 'Test Product Description', 100, [1], [], 'Product title cannot be empty'];
        yield ['Test Product', 'Test Product Description', -100, [1], [], 'Product price cannot be negative'];
        yield ['Test Product', 'Test Product Description', 'this is text', [1], [], 'Product price should be a number'];
        yield ['Test Product', 'Test Product Description', 10, [], [], 'Product requires a category'];
        yield ['Test Product', 'Test Product Description', 10, [10], [], 'Product requires a category that exists'];

        $invalidAttribute = [0 => ['attribute' => 1, 'value' => '']];
        yield ['Test Product', 'Test Product Description', 10, [1], $invalidAttribute, 'Product attribute value cannot be empty'];

        $invalidAttribute = [0 => ['attribute' => 10, 'value' => 'Test']];
        yield ['Test Product', 'Test Product Description', 10, [1], $invalidAttribute, 'Product attribute requires id that exists'];

        $invalidAttribute = [0 => ['attribute' => null, 'value' => 'Test']];
        yield ['Test Product', 'Test Product Description', 10, [1], $invalidAttribute, 'Product attribute requires an id'];
    }
}
