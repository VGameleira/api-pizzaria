<?php

/**
 * API REST - Criar uma nova pizza
 * 
 * Método HTTP: POST
 * Corpo esperado (JSON): { "nome": "...", "ingredientes": "...", "valor": 39.90 }
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Response.php';
require_once __DIR__ . '/../../models/Pizza.php';

Response::setupCORS('POST, OPTIONS');
Response::requireMethod('POST');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        Response::error(500, 'Erro ao conectar ao banco de dados');
    }

    $pizza = new Pizza($db);
    $data = Response::getRequestBody();

    // Validar campos obrigatórios
    Response::validateRequired($data, ['nome', 'ingredientes', 'valor']);

    // Sanitizar e tipar
    $pizza->nome         = trim($data->nome);
    $pizza->ingredientes = trim($data->ingredientes);
    $pizza->valor        = (float) $data->valor;

    // Validações de negócio
    $nomeLength = mb_strlen($pizza->nome);
    if ($nomeLength < 3 || $nomeLength > 50) {
        Response::error(400, 'Nome deve ter entre 3 e 50 caracteres');
    }

    $ingredientesLength = mb_strlen($pizza->ingredientes);
    if ($ingredientesLength < 5 || $ingredientesLength > 255) {
        Response::error(400, 'Ingredientes devem ter entre 5 e 255 caracteres');
    }

    if ($pizza->valor <= 0) {
        Response::error(400, 'Valor deve ser maior que zero');
    }

    if ($pizza->create()) {
        Response::created('Pizza criada com sucesso');
    }

    Response::error(500, 'Não foi possível criar a pizza');
} catch (Exception $e) {
    Response::error(500, 'Erro ao criar pizza');
}

