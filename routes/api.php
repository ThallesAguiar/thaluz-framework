<?php

use Core\Response;
use Core\Router;

Router::get('/api', function () {
    return Response::json('Bem-vindo ao thaluz API');
});

Router::get('/api/ping', function () {
    Response::json(
        'thaluz API online',
    );
});

// Rotas publicas
Router::post('/api/users', 'UserController@store');
Router::post('/api/login', 'AuthController@login');
Router::post('/api/refresh', 'AuthController@refresh');

// Rotas protegidas
Router::group(['middleware' => ['auth']], function () {
    Router::get('/api/me', 'AuthController@me');
    Router::post('/api/logout', 'AuthController@logout');

    Router::get('/api/users', 'UserController@index');
    Router::get('/api/users/{id}', 'UserController@show');
    Router::put('/api/users/{id}', 'UserController@update');
    Router::delete('/api/users/{id}', 'UserController@destroy');

    Router::get('/api/projects', 'ProjectController@index');

    Router::get('/api/query/mentor', 'ProjectController@test');
});
