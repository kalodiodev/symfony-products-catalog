<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Brand;
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
    public function index_brands()
    {
        $this->logIn();

        $this->client->catchExceptions(false);
        $crawler = $this->client->request('GET', '/admin/brands');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Brands');
        $this->assertCount(3, $crawler->filter('tbody > tr'));
    }

    /** @test */
    public function guest_cannot_create_brand()
    {
        $this->client->request('GET', '/admin/brands/create');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_create_a_brand()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/brands/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create Brand');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function a_user_can_store_a_brand()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/brands/create');
        $this->client->submitForm('Save Brand', $this->brandFormData());

        $this->assertResponseRedirects('/admin/brands');

        $brand = $this->entityManager->getRepository(Brand::class)
            ->findOneBy(['name' => $this->brandFormData()['brand[name]']]);

        $this->assertNotNull($brand);
        $this->assertSame($this->brandFormData()['brand[name]'], $brand->getName());
        $this->assertSame($this->brandFormData()['brand[slug]'], $brand->getSlug());
    }

    /**
     * @test
     * @dataProvider invalidDataOverridesProvider
     */
    public function brand_create_validation($field, $value, $errorMsg)
    {
        $this->login();

        $this->client->request('GET', '/admin/brands/create');
        $this->client->submitForm('Save Brand', $this->brandFormData([$field => $value]));

        $this->assertRouteSame('admin_brands_create');

        $count = $this->entityManager->getRepository(Brand::class)->count([]);
        $this->assertEquals(3, $count, $errorMsg);
    }

    /** @test */
    public function a_user_can_update_a_brand()
    {
        $this->logIn();

        $this->client->catchExceptions(false);

        $this->client->request('GET', '/admin/brands/1/edit');
        $this->client->submitForm('Save Brand', $this->brandFormData());

        $this->assertResponseRedirects('/admin/brands');

        $brand = $this->entityManager->getRepository(Brand::class)->find(1);

        $this->assertNotNull($brand);
        $this->assertSame('MyBrand', $brand->getName());
        $this->assertSame('my-brand', $brand->getSlug());
        $this->assertEquals(3, $this->entityManager->getRepository(Brand::class)->count([]));
    }

    /** @test */
    public function a_guest_cannot_update_a_brand()
    {
        $this->client->request('GET', '/admin/brands/1/edit');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_guest_cannot_delete_a_brand()
    {
        $this->client->request('POST', '/admin/brands/1/delete');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_delete_a_brand()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/brands/1/edit');
        $this->client->submitForm('Delete Brand');

        $this->assertResponseRedirects('/admin/brands');

        $brand = $this->entityManager->getRepository(Brand::class)->find(1);

        $this->assertNull($brand);
    }

    public function invalidDataOverridesProvider()
    {
        yield ['brand[name]', '', 'Brand name cannot be empty'];  // Empty name
        yield ['brand[name]', 'One', 'Brand name should be unique']; // Duplicate name
        yield ['brand[name]', 'a', 'Brand name min length'];  // Min name length
        yield ['brand[name]', $this->generateRandomString(81), 'Brand name max length'];  // Max name length
        yield ['brand[slug]', '', 'Brand slug cannot be empty'];  // Empty slug
        yield ['brand[slug]', 'one', 'Brand slug should be unique']; // Duplicate slug
    }

    private function brandFormData($overrides = [])
    {
        return array_merge([
            'brand[name]' => 'MyBrand',
            'brand[details]' => 'Test brand',
            'brand[slug]' => 'my-brand'
        ], $overrides);
    }
}