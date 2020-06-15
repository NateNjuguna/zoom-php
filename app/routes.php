<?php

use Zoom\Routing\Router;

Router::get('/auth/forgot', 'AuthController@getForgot');
Router::post('/auth/forgot', 'AuthController@postForgot');
Router::get('/auth/login', 'AuthController@getLogin');
Router::post('/auth/login', 'AuthController@postLogin');
Router::get('/auth/logout', 'AuthController@getLogout');
Router::get('/auth/reset/{token}', 'AuthController@getReset');
Router::post('/auth/reset', 'AuthController@postReset');
Router::get('/', 'IndexController@index');


Router::get('/404', function() {
	response()->httpCode(404);
	echo view();
});
