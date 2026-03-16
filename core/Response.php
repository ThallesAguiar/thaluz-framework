<?php

namespace Core;

class Response
{
    /**
     * Resposta de Sucesso Padronizada
     */
    public static function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode([
            'status' => $status,
            'result' => $data
        ]);
        exit;
    }

    /**
     * Erro seguindo RFC 7808 (Problem Details for HTTP APIs)
     */
    public static function error($title, $detail, $status = 400, $errors = [], $type = 'about:blank')
    {
        header('Content-Type: application/problem+json');
        http_response_code($status);

        echo json_encode([
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => $_SERVER['REQUEST_URI'],
            'errors' => $errors
        ]);
        exit;
    }
}
