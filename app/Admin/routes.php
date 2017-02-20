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

    //书籍管理
    $router->resources([
        'books' =>  'BooksController'
    ]);
//    //书籍列表
//    $router->get('/books/index','BooksController@index');
//    //删除书籍
//    $router->get('/books/delete','BooksController@delete');

    //图书分类管理
    $router->resources([
        'booksType' =>  'BooksTypeController'
    ]);

});

