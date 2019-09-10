<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Tests\DbWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    public function store_product_validation($productData)
    {
        $this->logIn();

        $this->client->catchExceptions(false);

        $crawler = $this->client->request('GET', '/admin/products/create');
        $form = $crawler->selectButton('Save Product')->form();

        $values = $this->productFormData();
        $values['product']['_token'] = $form['product[_token]']->getValue();

        $values['product']['title'] = $productData['title'];
        $values['product']['meta_title'] = $productData['meta_title'];
        $values['product']['description'] = $productData['description'];
        $values['product']['meta_description'] = $productData['meta_description'];
        $values['product']['sku'] = $productData['sku'];
        $values['product']['mpn'] = $productData['mpn'];
        $values['product']['price'] = $productData['price'];
        $values['product']['quantity'] = $productData['quantity'];
        $values['product']['brand'] = $productData['brand'];
        $values['product']['enabled'] = $productData['enabled'];
        $values['product']['categories'] = $productData['categories'];
        $values['product']['attributes'] = $productData['attributes'];

        $this->client->request('POST', '/admin/products/create', $values);

        $this->assertRouteSame('admin_products_create');

        $product = $this->entityManager->getRepository(Product::class)
            ->findOneBy(['title' => 'Test Product']);

        $this->assertNull($product, $productData['errorMsg']);
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

    /** @test */
    public function a_guest_cannot_upload_a_product_image()
    {
        $this->client->request('POST', '/admin/products/1/image');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_upload_a_product_image()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/products/1');
        $form = $crawler->selectButton('Upload Image')->form();

        $this->client->request('POST', '/admin/products/1/image', [
            'product_image' => [
                '_token' => $form['product_image[_token]']->getValue(),
                'image' => $this->createFakeFile('test', 'png')
            ]
        ]);

        $this->assertResponseRedirects('/admin/products/1');

        $productImages = $this->entityManager->getRepository(ProductImage::class)->findAll();

        $this->assertCount(1, $productImages);
    }

    protected function createFakeFile($filename, $extension, $width = 10, $height = 10): UploadedFile
    {
        $file = tempnam(sys_get_temp_dir(), 'upl') . '.' . $extension;
        imagepng(imagecreatetruecolor(10, 10), $file);
        return new UploadedFile(
            $file,
            $filename,
            'image/png',
            null,
            true
        );
    }

    private function productFormData()
    {
        return array_merge([
            'product' => [
                '_token' => '',
                'title' => 'Test Product',
                'meta_title' => 'test product',
                'description' => 'Test Product Description',
                'meta_description' => 'test product description',
                'sku' => 'A-100',
                'mpn' => '100',
                'price' => 100,
                'enabled' => true,
                'quantity' => 10,
                'brand' => 1,
                'slug' => null,
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
        yield $this->validationOverrideData('title', '', 'Product title cannot be empty');
        yield $this->validationOverrideData('price', -100, 'Product Price cannot be negative');
        yield $this->validationOverrideData('price', 'this is text', 'Product price should be a number');
        yield $this->validationOverrideData('quantity', null, 'Product quantity is required');
        yield $this->validationOverrideData('quantity', -10, 'Product quantity should be positive or zero');
        yield $this->validationOverrideData('sku', '', 'Product sku is required');
        yield $this->validationOverrideData('brand', null, 'Product brand is required');
        yield $this->validationOverrideData('enabled', null, 'Product enabled status is required');
        yield $this->validationOverrideData('categories', [], 'Product requires a category');
        yield $this->validationOverrideData('categories', [10], 'Product requires a category that exists');

        $invalidAttr = [0 => ['attribute' => 1, 'value' => '']];
        yield $this->validationOverrideData('attributes', $invalidAttr, 'Product attribute cannot be empty');

        $invalidAttr = [0 => ['attribute' => 10, 'value' => 'Test']];
        yield $this->validationOverrideData('attributes', $invalidAttr, 'Product attribute requires id that exists');

        $invalidAttr = [0 => ['attribute' => null, 'value' => 'Test']];
        yield $this->validationOverrideData('attributes', $invalidAttr, 'Product attribute requires an id');
    }

    private function validationOverrideData($field, $value, $errorMsg = '')
    {
        $product = $this->productFormData();
        $product['product'][$field] = $value;
        $product['product']['errorMsg'] = $errorMsg;

        return $product;
    }
}
