<?php

use Core\Response;
use Core\Router;

Router::get('/api', function () {
    return Response::json(["message" => "Bem-vindo ao Thaluz API"]);
});

// Listar todos os usuários
Router::get('/api/users', 'UserController@index');

// Criar um novo usuário
Router::post('/api/users', 'UserController@store');

// Mostrar um usuário específico
Router::get('/api/users/{id}', 'UserController@show');

// Atualizar um usuário
Router::put('/api/users/{id}', 'UserController@update');

// Deletar um usuário
Router::delete('/api/users/{id}', 'UserController@destroy');
