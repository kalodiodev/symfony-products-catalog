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

    private function brandFormData($overrides = [])
    {
        return array_merge([
            'brand[name]' => 'MyBrand',
            'brand[details]' => 'Test brand',
            'brand[slug]' => 'my-brand'
        ], $overrides);
    }
}