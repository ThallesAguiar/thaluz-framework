<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\JwtService;
use Core\Request;
use Core\Response;

class AuthController extends Controller
{
    private function sanitizeUser(array $user): array
    {
        unset($user['password']);
        return $user;
    }

    public function login()
    {
        $data = Request::all();

        if (empty($data['email']) || empty($data['password'])) {
            return Response::error(
                'Validation Error',
                'Os campos "email" e "password" sao obrigatorios.',
                422
            );
        }

        $user = User::findByEmail($data['email']);

        if (!$user || empty($user['password']) || !password_verify($data['password'], $user['password'])) {
            return Response::error('Unauthorized', 'Credenciais invalidas.', 401);
        }

        $accessToken = JwtService::makeAccessToken((int) $user['id']);
        $refreshToken = JwtService::makeRefreshToken((int) $user['id']);

        return Response::json(
            'Autenticação Concluída com Sucesso',
            [
                'access_token' => $accessToken['token'],
                'token_type' => 'Bearer',
                'expires_at' => $accessToken['expires_at'],
                'refresh_token' => $refreshToken['token'],
                'refresh_expires_at' => $refreshToken['expires_at'],
                'user' => $this->sanitizeUser($user),
            ]
        );
    }

    public function refresh()
    {
        $data = Request::all();
        $providedToken = $data['refresh_token'] ?? null;

        if (!$providedToken) {
            return Response::error('Validation Error', 'O campo "refresh_token" e obrigatorio.', 422);
        }

        try {
            $claims = JwtService::decodeRefreshToken($providedToken);
        } catch (\Firebase\JWT\ExpiredException $exception) {
            return Response::error('Unauthorized', 'Refresh token expirado.', 401);
        } catch (\Throwable $th) {
            return Response::error('Unauthorized', 'Refresh token invalido.', 401);
        }

        $userId = (int) ($claims['sub'] ?? 0);
        if ($userId <= 0) {
            return Response::error('Unauthorized', 'Claims do token invalidas.', 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return Response::error('Unauthorized', 'Usuario da sessao nao foi encontrado.', 401);
        }

        $accessToken = JwtService::makeAccessToken((int) $user['id']);
        $refreshToken = JwtService::makeRefreshToken((int) $user['id']);

        return Response::json(
            'Autenticação Renovada com Sucesso',
            [
                'access_token' => $accessToken['token'],
                'token_type' => 'Bearer',
                'expires_at' => $accessToken['expires_at'],
                'refresh_token' => $refreshToken['token'],
                'refresh_expires_at' => $refreshToken['expires_at'],
                'user' => $this->sanitizeUser($user),    
            ]
        );
    }

    public function me()
    {
        $user = Request::user();

        if (!$user) {
            return Response::error(
                'Unauthorized', 
                'Usuario nao autenticado.', 
                401
            );
        }

        return Response::json(
            'Usuário autenticado',
            $this->sanitizeUser($user)
        );
    }

    public function logout()
    {
        return Response::json(
            'Logout stateless: descarte access_token e refresh_token no cliente.',
        );
    }
}
