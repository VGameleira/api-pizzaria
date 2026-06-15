<?php

/**
 * Utilitário para padronizar respostas HTTP JSON da API.
 * 
 * Centraliza cabeçalhos CORS, códigos de status e formatação
 * para garantir consistência em todos os endpoints.
 */
class Response
{
    /**
     * Configura os cabeçalhos CORS e Content-Type uma única vez.
     */
    public static function setupCORS(string $allowedMethods = 'GET, POST, PUT, DELETE, OPTIONS'): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: ' . $allowedMethods);
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
        header('Content-Type: application/json; charset=UTF-8');

        // Responde imediatamente a requisições OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    /**
     * Verifica se o método HTTP corresponde ao esperado.
     */
    public static function requireMethod(string $expected): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $expected) {
            self::json(405, [
                'sucesso'  => false,
                'mensagem' => "Método HTTP não permitido. Use $expected.",
            ]);
        }
    }

    /**
     * Retorna uma resposta JSON padronizada e encerra a execução.
     */
    public static function json(int $statusCode, array $data): never
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Retorna uma resposta de sucesso com dados.
     */
    public static function success(array $data = [], string $message = 'Operação realizada com sucesso'): never
    {
        self::json(200, [
            'sucesso'  => true,
            'mensagem' => $message,
            'dados'    => $data,
        ]);
    }

    /**
     * Retorna uma resposta de erro.
     */
    public static function error(int $statusCode, string $message): never
    {
        self::json($statusCode, [
            'sucesso'  => false,
            'mensagem' => $message,
        ]);
    }

    /**
     * Retorna uma resposta 201 (Created).
     */
    public static function created(string $message = 'Registro criado com sucesso'): never
    {
        self::json(201, [
            'sucesso'  => true,
            'mensagem' => $message,
        ]);
    }

    /**
     * Valida campos obrigatórios no payload recebido.
     */
    public static function validateRequired(object $data, array $fields): void
    {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data->$field) || (is_string($data->$field) && trim($data->$field) === '')) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            self::json(400, [
                'sucesso'  => false,
                'mensagem' => 'Campos obrigatórios não fornecidos: ' . implode(', ', $missing),
                'campos'   => $missing,
            ]);
        }
    }

    /**
     * Obtém e decodifica o corpo da requisição JSON.
     */
    public static function getRequestBody(): object
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::error(400, 'JSON inválido: ' . json_last_error_msg());
        }

        return $data;
    }
}