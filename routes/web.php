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

$router->group(['prefix'=>'auth'], function($router) {

	$router->get('check-token','AuthController@checkToken');
		
	$router->post('login','AuthController@login');

	$router->post('check-user-provider','AuthController@checkUserProvider');

	$router->group(['middleware'=>'jwt'], function($router) {

		$router->get('logout','AuthController@logout');

	});

	$router->post('register','RegisterController@register');
	
	$router->post('register/active-account','RegisterController@activeAccount');
});

$router->group(['prefix'=>'admin-auth'], function($router) {

	$router->get('check-token','AdminAuthController@checkToken');
		
	$router->post('login','AdminAuthController@login');

	// $router->post('check-user-provider','AuthController@checkUserProvider');

	// $router->group(['middleware'=>'jwt'], function($router) {

	// 	$router->get('logout','AuthController@logout');

	// });

	// $router->post('register','RegisterController@register');
	
	// $router->post('register/active-account','RegisterController@activeAccount');
});
