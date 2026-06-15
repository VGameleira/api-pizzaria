<?php

/**
 * API REST - Deletar uma pizza
 * 
 * Método HTTP: DELETE
 * Corpo esperado (JSON): { "id": 1 }
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Response.php';
require_once __DIR__ . '/../../models/Pizza.php';

Response::setupCORS('DELETE, OPTIONS');
Response::requireMethod('DELETE');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        Response::error(500, 'Erro ao conectar ao banco de dados');
    }

    $pizza = new Pizza($db);
    $data = Response::getRequestBody();

    // Validar ID
    if (!isset($data->id) || empty($data->id)) {
        Response::error(400, 'ID da pizza é obrigatório');
    }

    $pizza->idPizza = (int) $data->id;

    if ($pizza->idPizza <= 0) {
        Response::error(400, 'ID da pizza inválido');
    }

    if ($pizza->delete()) {
        Response::success([], 'Pizza deletada com sucesso');
    }

    Response::error(500, 'Não foi possível deletar a pizza. Verifique se o ID existe.');
} catch (Exception $e) {
    Response::error(500, 'Erro ao deletar pizza');
}
