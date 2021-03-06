<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Facades\Auth;

class UsersTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = Administrator::first();

        Auth::login($this->user);
    }

    public function testUsersIndexPage()
    {
        $this->visit('admin/auth/users')
            ->see('Administrator');
    }

    public function testCreateUser()
    {
        $user = [
            'username' => 'Test',
            'name'     => 'Name',
            'password' => '123456',
        ];

        $this->visit('admin/auth/users/create')
            ->see('Create')
            ->submitForm('Submit', $user)
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.users_table'), ['username' => 'Test']);

        $this->visit('admin/auth/logout')
            //->dontSeeIsAuthenticated('admin')
            ->seePageIs('admin/auth/login')
            ->submitForm('Login', ['username' => $user['username'], 'password' => $user['password']])
            ->see('dashboard')
            //->seeIsAuthenticated('admin')
            ->seePageIs('admin');
    }

    public function testUpdateUser()
    {
        $this->visit('admin/auth/users/'.$this->user->id.'/edit')
            ->see('Create')
            ->submitForm('Submit', ['name' => 'test'])
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.users_table'), ['name' => 'test']);
    }

    public function testResetPassword()
    {
        $password = 'odjwyufkglte';

        $this->visit('admin/auth/users/'.$this->user->id.'/edit')
            ->see('Create')
            ->submitForm('Submit', ['password' => $password])
            ->seePageIs('admin/auth/users')
            ->visit('admin/auth/logout')
            //->dontSeeIsAuthenticated('admin')
            ->seePageIs('admin/auth/login')
            ->submitForm('Login', ['username' => $this->user->username, 'password' => $password])
            ->see('dashboard')
            //->seeIsAuthenticated('admin')
            ->seePageIs('admin');
    }
}
