<?php

/**
 * API REST - Deletar uma bebida
 * 
 * Método HTTP: DELETE
 * Corpo esperado (JSON): { "id": 1 }
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Response.php';
require_once __DIR__ . '/../../models/Bebida.php';

Response::setupCORS('DELETE, OPTIONS');
Response::requireMethod('DELETE');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        Response::error(500, 'Erro ao conectar ao banco de dados');
    }

    $bebida = new Bebida($db);
    $data = Response::getRequestBody();

    // Validar ID
    if (!isset($data->id) || empty($data->id)) {
        Response::error(400, 'ID da bebida é obrigatório');
    }

    $bebida->idBebida = (int) $data->id;

    if ($bebida->idBebida <= 0) {
        Response::error(400, 'ID da bebida inválido');
    }

    if ($bebida->delete()) {
        Response::success([], 'Bebida deletada com sucesso');
    }

    Response::error(500, 'Não foi possível deletar a bebida. Verifique se o ID existe.');
} catch (Exception $e) {
    Response::error(500, 'Erro ao deletar bebida');
}
