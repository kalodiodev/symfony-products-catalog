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
        $this->assertCount(2, $crawler->filter('tbody > tr'));
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

    /** @test */
    public function a_guest_cannot_update_password_of_user()
    {
        $this->client->request('POST', '/admin/users/1/password');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_update_password_of_user()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/1/edit');
        $this->client->submitForm('Update Password', [
            'user[password][first]' => 'password',
            'user[password][second]' => 'password'
        ]);

        $this->assertResponseRedirects('/admin/users');

        $user = $this->entityManager->getRepository(User::class)->find(1);

        $this->assertNotNull($user);
    }

    /** @test */
    public function a_guest_cannot_create_a_user()
    {
        $this->client->request('GET', '/admin/users/create');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_create_a_user()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create User');
        $this->assertSelectorExists('form');
    }

    /** @test */
    public function a_user_can_store_a_user()
    {
        $this->login();

        $this->client->catchExceptions(false);

        $this->client->request('GET', '/admin/users/create');
        $this->client->submitForm('Save User', $this->userFormData([
            'user[password][first]' => 'password',
            'user[password][second]' => 'password'
        ]));

        $this->assertResponseRedirects('/admin/users');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['name' => 'Jane Smith']);

        $this->assertNotNull($user);
        $this->assertSame('jane@example.com', $user->getEmail());
    }

    /**
     * @test
     * @dataProvider invalidDataOverridesProvider
     */
    public function user_create_validation($field, $value, $errorMsg)
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/create');
        $this->client->submitForm('Save User', $this->userFormData([$field => $value], true));

        $this->assertRouteSame('admin_users_create');

        $count = $this->entityManager->getRepository(User::class)->count([]);
        $this->assertEquals(2, $count, $errorMsg);
    }

    /** @test */
    public function a_guest_cannot_delete_a_user()
    {
        $this->client->request('POST', '/admin/users/1/delete');

        $this->assertResponseRedirects('/login');
    }

    /** @test */
    public function a_user_can_delete_a_user()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/2/edit');
        $this->client->submitForm('Delete User');

        $this->assertResponseRedirects('/admin/users');

        $user = $this->entityManager->getRepository(User::class)->find(2);

        $this->assertNull($user);
    }

    /** @test */
    public function user_logged_out_if_deletes_himself()
    {
        $this->logIn();

        $this->client->request('GET', '/admin/users/1/edit');
        $this->client->submitForm('Delete User');

        $this->assertResponseRedirects('/login');

        $user = $this->entityManager->getRepository(User::class)->find(1);

        $this->assertNull($user);
    }

    private function userFormData($overrides = [], $withPassword = false)
    {
        $data = [
            'user[name]' => 'Jane Smith',
            'user[email]' => 'smith@example.com'
        ];

        if ($withPassword) {
            $data['user[password][first]'] = 'password';
            $data['user[password][second]'] = 'password';
        }

        return array_merge($data, $overrides);
    }

    public function invalidDataOverridesProvider()
    {
        yield ['user[name]', '', 'User name cannot be empty'];
        yield ['user[name]', 'a', 'User name min length'];
        yield ['user[name]', $this->generateRandomString(193), 'User name max length'];
        yield ['user[email]', '', 'User email cannot be empty'];
        yield ['user[email]', 'text', 'User email should be an email'];
        yield ['user[email]', 'test@example.com', 'User email should be unique'];
        yield ['user[password][first]', '', 'Password is required'];
        yield ['user[password][second]', '', 'Password should be confirmed'];
        yield ['user[password][second]', 'wrongpassword', 'Passwords should match'];
    }
}