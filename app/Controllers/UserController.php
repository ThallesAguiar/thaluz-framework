<?php

namespace App\Controllers;

use App\Models\User;
use Core\Request;
use Core\Response;

class UserController extends Controller
{
    public function index()
    {
        return $this->json(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::error(
                'User Not Found',
                "O usuário com ID $id não foi encontrado.",
                404
            );
        }

        return $this->json($user);
    }

    public function store()
    {
        $data = Request::all();

        if (empty($data['name'])) {
            return Response::error(
                'Validation Error',
                'O campo "name" é obrigatório.',
                422
            );
        }

        $user = User::create($data);
        return $this->json($user, 201);
    }

    public function update($id)
    {
        $data = Request::all();
        $user = User::update($id, $data);

        if (!$user) {
            return Response::error(
                'User Not Found',
                "O usuário com ID $id não foi encontrado para atualização.",
                404
            );
        }

        return $this->json($user);
    }

    public function destroy($id)
    {
        $deleted = User::delete($id);

        if (!$deleted) {
            return Response::error(
                'User Not Found',
                "O usuário com ID $id não foi encontrado para exclusão.",
                404
            );
        }

        return $this->json(['message' => 'Usuário deletado com sucesso']);
    }
}
