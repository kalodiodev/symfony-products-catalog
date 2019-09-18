<?php

namespace App\Tests\Controller;

use App\Tests\DbWebTestCase;

class CategoryControllerTest extends DbWebTestCase
{
    /** @test */
    public function index_category_products()
    {
        $this->client->request('GET', '/categories/smartphones');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Smartphones');
    }
}