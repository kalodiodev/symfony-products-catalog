<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Tests\DbWebTestCase;

class UserControllerTest extends DbWebTestCase
{
    /** @test */
    public function a_guest_cannot_index_users()
    {
        $this->client->request('GET', '/admin/users');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function index_users()
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Users');
        $this->assertCount(1, $crawler->filter('tbody > tr'));
    }

    /** @test */
    public function a_guest_cannot_edit_user()
    {
        $this->client->request('GET', '/admin/users/1/edit');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_edit_a_user()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/1/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Update User');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function a_user_can_update_a_user()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/1/edit');
        $this->client->submitForm('Save User', $this->userFormData());

        $this->assertResponseRedirects('/admin/users');

        $user = $this->entityManager->getRepository(User::class)->find(1);

        $this->assertNotNull($user);
        $this->assertSame($this->userFormData()['user[name]'], $user->getName());
        $this->assertSame($this->userFormData()['user[email]'], $user->getEmail());
    }

    private function userFormData($overrides = [])
    {
        return array_merge([
            'user[name]' => 'Jane Smith',
            'user[email]' => 'jane@example.com'
        ], $overrides);
    }
}