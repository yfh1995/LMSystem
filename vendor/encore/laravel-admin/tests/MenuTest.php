<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Facades\Auth;

class MenuTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Auth::login(Administrator::first());
    }

    public function testMenuIndex()
    {
        $this->visit('admin/auth/menu')
            ->see('Menu')
            ->see('Index')
            ->see('Auth')
            ->see('Users')
            ->see('Roles')
            ->see('Permission')
            ->see('Menu');
    }

    public function testAddMenu()
    {
        $item = ['parent_id' => '0', 'title' => 'Test', 'uri' => 'test'];

        $this->visit('admin/auth/menu')
            ->click('New')
            ->seePageIs('admin/auth/menu/create')
            ->see('Menu')
            ->see('Tips')
            ->submitForm('Submit', $item)
            ->seePageIs('admin/auth/menu')
            ->seeInDatabase(config('admin.database.menu_table'), $item)
            ->assertEquals(7, Menu::count());

        $this->setExpectedException(Illuminate\Foundation\Testing\HttpException::class);

        $this->visit('admin')
            ->see('Test')
            ->click('Test');
    }

    public function testDeleteMenu()
    {
        $this->delete('admin/auth/menu/8')
            ->assertEquals(6, Menu::count());
    }

    public function testEditMenu()
    {
        $this->visit('admin/auth/menu/1/edit')
            ->see('Menu')
            ->submitForm('Submit', ['title' => 'blablabla'])
            ->seePageIs('admin/auth/menu')
            ->seeInDatabase(config('admin.database.menu_table'), ['title' => 'blablabla'])
            ->assertEquals(6, Menu::count());
    }
}
