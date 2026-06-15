<?php

/**
 * API REST - Atualizar uma bebida
 * 
 * Método HTTP: PUT
 * Corpo esperado (JSON): { "id": 1, "nome": "...", "alcoolica": 0|1, "valor": 19.90 }
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Response.php';
require_once __DIR__ . '/../../models/Bebida.php';

Response::setupCORS('PUT, OPTIONS');
Response::requireMethod('PUT');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        Response::error(500, 'Erro ao conectar ao banco de dados');
    }

    $bebida = new Bebida($db);
    $data = Response::getRequestBody();

    // Validar campos obrigatórios
    Response::validateRequired($data, ['id', 'nome', 'alcoolica', 'valor']);

    // Sanitizar e tipar
    $bebida->idBebida  = (int) $data->id;
    $bebida->nome      = trim($data->nome);
    $bebida->alcoolica = (int) $data->alcoolica;
    $bebida->valor     = (float) $data->valor;

    // Validações de negócio
    $nomeLength = mb_strlen($bebida->nome);
    if ($nomeLength < 3 || $nomeLength > 50) {
        Response::error(400, 'Nome deve ter entre 3 e 50 caracteres');
    }

    if (!in_array($bebida->alcoolica, [0, 1], true)) {
        Response::error(400, 'alcoolica deve ser 0 (não alcoólica) ou 1 (alcoólica)');
    }

    if ($bebida->valor <= 0) {
        Response::error(400, 'Valor deve ser maior que zero');
    }

    if ($bebida->update()) {
        Response::success([], 'Bebida atualizada com sucesso');
    }

    Response::error(500, 'Não foi possível atualizar a bebida');
} catch (Exception $e) {
    Response::error(500, 'Erro ao atualizar bebida');
}