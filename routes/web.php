<?php

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


use App\Helpers\Api;
$router->get('/', function () use ($router) {
    $data = $router->app->version();
    return response()->json(Api::format(1, $data, 'Success'), 200);
});

$router->get('/curl-sample', 'ExampleController@index'); 

$router->post('auth/login','AuthController@login');
$router->post('auth/register','AuthController@register');
$router->get('auth/check-token','AuthController@checkToken');

$router->group(['middleware'=>'jwt','prefix'=>'auth'], function($router) {
	$router->get('/me', 'ExampleController@me');

	$router->post('logout','AuthController@logout');
});
