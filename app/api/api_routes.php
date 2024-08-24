<?php

return [
    'GET' => [
        'users' => 'App\Controllers\Api\UserController@index',
        'users/{id}' => 'App\Controllers\Api\UserController@show',
        'posts' => 'App\Controllers\Api\PostController@index',
        'comments' => 'App\Controllers\Api\PostController@getComments',
    ],
    'POST' => [
        'users' => 'App\Controllers\Api\UserController@store',
        'login' => 'App\Controllers\Api\UserController@login',
        'posts' => 'App\Controllers\Api\PostController@store',
    ],
    'PUT' => [
        'users/{id}' => 'App\Controllers\Api\UserController@update'
    ],
    'DELETE' => [
        'users/{id}' => 'App\Controllers\Api\UserController@destroy'
    ],
];
