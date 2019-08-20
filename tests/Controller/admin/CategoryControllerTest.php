<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Category;
use App\Tests\DbWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * @test
     * @dataProvider invalidDataOverridesProvider
     */
    public function category_create_validation($field, $value)
    {
        $this->login();

        $this->client->request('GET', '/admin/categories/create');
        $this->client->submitForm('Save Category', $this->categoryFormData([$field => $value]));

        $this->assertRouteSame('admin_categories_create');

        $count = $this->entityManager->getRepository(Category::class)->count([]);
        $this->assertEquals(3, $count);
    }

    /** @test */
    public function a_user_can_update_a_category()
    {
        $this->logIn();

        $this->client->catchExceptions(false);

        $this->client->request('GET', '/admin/categories/smartphones/edit');
        $this->client->submitForm('Save Category', $this->categoryFormData());

        $this->assertResponseRedirects('/admin/categories');

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Testing']);

        $this->assertNotNull($category);
        $this->assertSame('Testing', $category->getName());
        $this->assertSame('testing', $category->getSlug());
        $this->assertEquals(3,  $this->entityManager->getRepository(Category::class)->count([]));
    }

    /**
     * @test
     */
    public function cannot_update_a_category_that_does_not_exist()
    {
        $this->logIn();

        $this->client->catchExceptions(false);

        $this->expectException(NotFoundHttpException::class);

        $this->client->request('GET', '/admin/categories/toys/edit');
    }

    public function invalidDataOverridesProvider()
    {
        yield ['category[name]', ''];  // Empty name
        yield ['category[name]', 'a'];  // Min name length
        yield ['category[name]', $this->generateRandomString(51)];  // Max name length
        yield ['category[slug]', ''];  // Empty slug
    }

    protected function categoryFormData($overrides = [])
    {
        return array_merge([
            'category[name]' => "Testing",
            'category[description]' => "This is a great category",
            'category[slug]' => "testing"
        ], $overrides);
    }

    private function generateRandomString(int $length): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return mb_substr(str_shuffle(str_repeat($chars, ceil($length / mb_strlen($chars)))), 1, $length);
    }
}
