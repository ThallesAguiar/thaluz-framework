<?php

namespace Core;

class Response
{
    private const ORACLE_CHARSET = 'Windows-1252';

    private static function utf8ize($data)
    {
        if (is_array($data)) {
            $out = [];
            foreach ($data as $key => $value) {
                $out[$key] = self::utf8ize($value);
            }
            return $out;
        }

        if (is_string($data)) {
            if (mb_check_encoding($data, 'UTF-8')) {
                return $data;
            }
            return mb_convert_encoding($data, 'UTF-8', self::ORACLE_CHARSET);
        }

        return $data;
    }
    /**
     * Resposta de Sucesso Padronizada
     */
    public static function json($message = 'Sucesso', $data = null, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);

        $data = self::utf8ize($data);
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'result' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    /**
     * Erro seguindo RFC 7808 (Problem Details for HTTP APIs)
     */
    public static function error($title, $detail, $status = 400, $errors = [], $type = 'about:blank')
    {
        header('Content-Type: application/problem+json');
        http_response_code($status);

        $title = self::utf8ize($title);
        $detail = self::utf8ize($detail);
        $errors = self::utf8ize($errors);

        echo json_encode([
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => $_SERVER['REQUEST_URI'],
            'errors' => $errors
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}
