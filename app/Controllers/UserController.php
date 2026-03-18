<?php

namespace App\Controllers;

use App\Models\User;
use Core\Request;
use Core\Response;

class UserController extends Controller
{
    private function sanitizeUser(array $user): array
    {
        unset($user['password']);
        return $user;
    }

    private function sanitizeUsers(array $users): array
    {
        return array_map(function ($user) {
            return $this->sanitizeUser($user);
        }, $users);
    }

    public function index()
    {
        return Response::json(null, $this->sanitizeUsers(User::all()));
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::error(
                'User Not Found',
                "O usuario com ID $id nao foi encontrado.",
                404
            );
        }

        return Response::json(null, $this->sanitizeUser($user));
    }

    public function store()
    {
        $data = Request::all();

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return Response::error(
                'Validation Error',
                'Os campos "name", "email" e "password" sao obrigatorios.',
                422
            );
        }

        if (User::findByEmail($data['email'])) {
            return Response::error(
                'Validation Error',
                'Ja existe usuario com esse email.',
                409 
            );
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = User::create($data);
        return Response::json('Usuário cadastrado com sucesso.', $this->sanitizeUser($user), 201);
    }

    public function update($id)
    {
        $data = Request::all();

        if (empty($data['name'])) {
            return Response::error(
                'Validation Error',
                'O campo "name" e obrigatorio.',
                422
            );
        }

        $existing = User::find($id);
        if (!$existing) {
            return Response::error(
                'User Not Found',
                "O usuario com ID $id nao foi encontrado para atualizacao.",
                404
            );
        }

        if (!empty($data['email']) && $data['email'] !== ($existing['email'] ?? null)) {
            $userByEmail = User::findByEmail($data['email']);
            if ($userByEmail && (int) $userByEmail['id'] !== (int) $id) {
                return Response::error(
                    'Validation Error',
                    'Ja existe usuario com esse email.',
                    409
                );
            }
        }

        $data['email'] = $data['email'] ?? ($existing['email'] ?? null);
        $user = User::update($id, $data);

        if (!$user) {
            return Response::error(
                'User Not Found',
                "O usuario com ID $id nao foi encontrado para atualizacao.",
                404
            );
        }

        return Response::json(null, $this->sanitizeUser($user));
    }

    public function destroy($id)
    {
        $deleted = User::delete($id);

        if (!$deleted) {
            return Response::error(
                'User Not Found',
                "O usuario com ID $id nao foi encontrado para exclusao.",
                404
            );
        }

        return Response::json('Usuario deletado com sucesso');
    }
}
