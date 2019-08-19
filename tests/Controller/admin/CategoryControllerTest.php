<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Category;
use App\Tests\DbWebTestCase;

class CategoryControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_index_categories()
    {
        $this->client->request('GET', '/admin/categories');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function index_categories()
    {
        $this->logIn();

//        $this->client->catchExceptions(false);
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Categories');
        $this->assertCount(3, $crawler->filter('tbody > tr'));
    }

    /** @test */
    public function guest_cannot_create_category()
    {
        $this->client->request('GET', '/admin/categories/create');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function create_category()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/categories/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create Category');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function guest_cannot_store_category()
    {
        $this->client->request('POST', '/admin/categories/create');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_store_category()
    {
        $this->logIn();

        $this->client->catchExceptions(false);

        $this->client->request('GET', '/admin/categories/create');
        $this->client->submitForm('Save Category', $this->categoryFormData());

        $this->assertResponseRedirects('/admin/categories');

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Testing']);

        $this->assertNotNull($category);
        $this->assertSame('Testing', $category->getName());
        $this->assertSame('testing', $category->getSlug());
    }

    protected function categoryFormData($overrides = [])
    {
        return array_merge([
            'category[name]' => "Testing",
            'category[description]' => "This is a great category",
            'category[slug]' => "testing"
        ], $overrides);
    }
}
