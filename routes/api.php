<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\ExampleController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function ($router) {
    $router->post('reset-password', 'UserController@resetPassword');
    $router->post('reset-password/validate', 'UserController@validateResetPassword');
    $router->put('change-password', 'UserController@changePassword');
    $router->post('test-notification/token', 'TestController@testUsingToken');
    $router->post('test-notification/topic', 'TestController@testUsingTopics');

    $router->group(['prefix' => 'auth'], function ($auth){
        $auth->post('login', 'AuthController@login');
        $auth->post('logout', 'AuthController@logout');
        $auth->post('refresh', 'AuthController@refresh');
        $auth->post('user-profile', 'AuthController@me');
    });

    $router->group(['prefix' => 'users'], function ($user){
        $user->get('/', 'UserController@getAll');
        $user->get('/{id}', 'UserController@getById');
        $user->post('/register', 'UserController@create');
        $user->post('/photo', 'UserController@updatePhoto');
        $user->put('/fcm-token', 'UserController@updateFcmToken');
        $user->post('/fcm-token/revoke', 'UserController@revokeFcmToken');
        $user->put('/password', 'UserController@updatePassword');
        $user->put('/{id}', 'UserController@update');
        $user->delete('/{id}', 'UserController@delete');
    });
});
