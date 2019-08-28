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

    /** @test */
    public function guest_cannot_create_attribute()
    {
        $this->client->request('GET', '/admin/attributes/create');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_create_an_attribute()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/attributes/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create Attribute');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function a_user_can_store_an_attribute()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/attributes/create');
        $this->client->submitForm('Save Attribute', $this->attributeFormData());

        $this->assertResponseRedirects('/admin/attributes');

        $attribute = $this->entityManager->getRepository(Attribute::class)
            ->findOneBy(['name' => $this->attributeFormData()['attribute[name]']]);

        $this->assertNotNull($attribute);
        $this->assertSame($this->attributeFormData()['attribute[description]'], $attribute->getDescription());
    }

    private function attributeFormData($overrides = [])
    {
        return array_merge([
            'attribute[name]' => 'Size',
            'attribute[description]' => 'Product size'
        ], $overrides);
    }
}