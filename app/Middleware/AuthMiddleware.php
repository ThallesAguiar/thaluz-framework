<?php

namespace App\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Core\Request;
use Core\Response;
use Firebase\JWT\ExpiredException;

class AuthMiddleware
{
    public function handle(array $params = []): void
    {
        $token = Request::bearerToken();

        if (!$token) {
            Response::error('Unauthorized', 'Token ausente. Use Authorization: Bearer {token}.', 401);
        }

        try {
            $claims = JwtService::decodeAccessToken($token);
        } catch (ExpiredException $exception) {
            Response::error('Unauthorized', 'Access token expirado.', 401);
        } catch (\Throwable $th) {
            Response::error('Unauthorized', 'Access token invalido.', 401);
        }

        $userId = (int) ($claims['sub'] ?? 0);

        if ($userId <= 0) {
            Response::error('Unauthorized', 'Claims do token invalidas.', 401);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::error('Unauthorized', 'Usuario nao encontrado.', 401);
        }

        Request::setUser($user);
        Request::set('auth_claims', $claims);
    }
}
