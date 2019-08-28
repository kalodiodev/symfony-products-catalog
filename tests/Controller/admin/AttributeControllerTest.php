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

    /**
     * @test
     * @dataProvider invalidDataOverridesProvider
     */
    public function attribute_create_validation($field, $value, $errorMsg)
    {
        $this->logIn();

        $this->client->request('GET', '/admin/attributes/create');
        $this->client->submitForm('Save Attribute', $this->attributeFormData([$field => $value]));

        $this->assertRouteSame('admin_attributes_create');

        $count = $this->entityManager->getRepository(Attribute::class)->count([]);
        $this->assertEquals(3, $count, $errorMsg);
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

    /** @test */
    public function a_user_can_update_an_attribute()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/attributes/1/edit');
        $this->client->submitForm('Save Attribute', $this->attributeFormData());

        $this->assertResponseRedirects('/admin/attributes');

        $attribute = $this->entityManager->getRepository(Attribute::class)->find(1);

        $this->assertNotNull($attribute);
        $this->assertSame($this->attributeFormData()['attribute[name]'], $attribute->getName());
        $this->assertSame($this->attributeFormData()['attribute[description]'], $attribute->getDescription());
        $this->assertEquals(3, $this->entityManager->getRepository(Attribute::class)->count([]));
    }

    /** @test */
    public function a_guest_cannot_delete_an_attribute()
    {
        $this->client->request('POST', '/admin/attributes/1/delete');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_delete_an_attribute()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/attributes/1/edit');
        $this->client->submitForm('Delete Attribute');

        $this->assertResponseRedirects('/admin/attributes');

        $attribute = $this->entityManager->getRepository(Attribute::class)->find(1);

        $this->assertNull($attribute);
    }

    public function invalidDataOverridesProvider()
    {
        yield ['attribute[name]', '', 'Attribute name cannot be empty'];
        yield ['attribute[name]', 'Color', 'Attribute name must be unique'];
        yield ['attribute[name]', 'a', 'Attribute name min length'];
        yield ['attribute[name]', $this->generateRandomString(193), 'Attribute name max length'];
        yield ['attribute[description]', $this->generateRandomString(256), 'Attribute description max length'];
    }

    private function attributeFormData($overrides = [])
    {
        return array_merge([
            'attribute[name]' => 'Size',
            'attribute[description]' => 'Product size'
        ], $overrides);
    }
}