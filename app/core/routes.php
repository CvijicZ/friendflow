<?php

// This file defines the routes for your application. Routes map URLs to controller actions.

// The routes array contains different methods (GET, POST, PUT, DELETE) as keys and their corresponding routes and controller actions as values.
// Format: '/url' => 'Controller@method'

// Routes with middleware: 
// Format: '/url' => ['middleware' => 'middleware_name', 'controller' => 'Controller@method']

return  [
    'GET' => [
        '/' => 'App\Controllers\HomeController@index',
        '/error' => 'App\Controllers\HomeController@error',
        '/login' => 'App\Controllers\AuthController@showLoginForm',
        '/logout' => 'App\Controllers\AuthController@logout',
        '/register' => 'App\Controllers\AuthController@showRegisterForm',
        '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\UserController@editProfile'],
    ],
    'POST' => [
        // Auth requests
        '/login' => 'App\Controllers\AuthController@login',
        '/register' => 'App\Controllers\AuthController@register',
        // User requests
        '/updateProfile' => 'App\Controllers\UserController@updateProfile',
        '/post' => 'App\Controllers\PostController@create',
        '/comment' => 'App\Controllers\CommentController@create',
        '/upload-profile-image' => 'App\Controllers\UserController@uploadProfileImage',
        '/get-comments' => 'App\Controllers\PostController@getComments',
        // HTTP requests related to the friend requests
        '/add-friend' => 'App\Controllers\FriendsController@addFriend',
        '/get-friend-requests' => 'App\Controllers\FriendsController@getFriendRequests',
        '/count-friend-requests' => 'App\Controllers\FriendsController@countFriendRequests',
        '/accept-friend-request' => 'App\Controllers\FriendsController@acceptFriendRequest',
        '/get-all-friends' => 'App\Controllers\FriendsController@getAllFriends',
        // Messages requests
        '/get-friend-messages' => 'App\Controllers\MessageController@getMessages',
        '/update-message-status' => 'App\Controllers\MessageController@updateStatus',
        '/count-unseen-messages' => 'App\Controllers\MessageController@countUnseenMessages',
    ],
    'PUT' => [
        '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\UserController@update'],
        '/post' => ['middleware' => 'auth', 'controller' => 'App\Controllers\PostController@update'],
    ],
    'DELETE' => [
        '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\UserController@delete'],
        '/post' => ['middleware' => 'auth', 'controller' => 'App\Controllers\PostController@delete'],
    ]
];