<?php

$router = app('admin.router');

$router->group(['middleware' => 'admin.auth'],function($router){

    $router->get('test','BooksController@getBookType');

    //=====================================================
    //==========                     ======================
    //==========         首页         ======================
    //==========                     ======================
    //=====================================================
    $router->get('/', 'HomeController@index');

    //=====================================================
    //==========                     ======================
    //==========      图书管理        ======================
    //==========                     ======================
    //=====================================================

    $router->resources([
        //图书管理
        'books'     =>  'BooksController',
        //图书分类管理
        'booksType' =>  'BooksTypeController'
    ]);

    //图书借阅管理
    //借阅列表
    $router->get('borrow','BorrowController@index');
    //跳转至创建借阅记录页面
    $router->get('borrow/create','BorrowController@create');
    //创建借阅记录
    $router->post('borrow','BorrowController@store');
    //归还图书
    $router->get('borrow/return','BorrowController@returnBook');
    //赔偿图书
    $router->get('borrow/compensate','BorrowController@compensate');
});

