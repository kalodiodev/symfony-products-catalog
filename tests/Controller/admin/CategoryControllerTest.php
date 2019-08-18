<?php

namespace App\Tests\Controller\Admin;

use App\Tests\DbWebTestCase;

class CategoryControllerTest extends DbWebTestCase
{
    public function testAGuestCannotIndexProducts()
    {
        $this->client->request('GET', '/admin/categories');

        $this->assertResponseRedirects('/login');
    }

    public function testIndexCategories()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Categories');
        $this->assertCount(3, $crawler->filter('tbody > tr'));
    }
}
