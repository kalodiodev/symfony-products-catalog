<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Attribute;
use App\Tests\DbWebTestCase;

class AttributeControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_index_attributes()
    {
        $this->client->request('GET', '/admin/attributes');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function index_attributes()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/admin/attributes');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Attributes');
        $this->assertCount(3, $crawler->filter('tbody > tr'));
    }
}