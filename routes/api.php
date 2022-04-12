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

    $router->group(['prefix' => 'auth'], function ($auth){
        $auth->post('login', 'AuthController@login');
        $auth->post('logout', 'AuthController@logout');
        $auth->post('refresh', 'AuthController@refresh');
        $auth->post('user-profile', 'AuthController@me');
    });

    $router->group(['prefix' => 'users'], function ($example){
        $example->get('/', 'UserController@getAll');
        $example->get('/{id}', 'UserController@getById');
        $example->post('/', 'UserController@create');
        $example->put('/fcm-token', 'UserController@updateFcmToken');
        $example->put('/password', 'UserController@updatePassword');
        $example->put('/{id}', 'UserController@update');
        $example->delete('/{id}', 'UserController@delete');
    });
});
