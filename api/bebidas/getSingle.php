<?php

/**
 * API REST - Obter uma única bebida pelo ID
 * 
 * Método HTTP: GET
 * Parâmetro query: ?idBebida=1
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Response.php';
require_once __DIR__ . '/../../models/Bebida.php';

Response::setupCORS('GET, OPTIONS');
Response::requireMethod('GET');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        Response::error(500, 'Erro ao conectar ao banco de dados');
    }

    if (!isset($_GET['idBebida']) || $_GET['idBebida'] === '') {
        Response::error(400, 'ID da bebida não fornecido');
    }

    $bebida = new Bebida($db);
    $bebida->idBebida = (int) $_GET['idBebida'];

    if (!$bebida->readSingle()) {
        Response::error(404, 'Bebida não encontrada');
    }

    Response::success($bebida->toArray());
} catch (Exception $e) {
    Response::error(500, 'Erro ao buscar bebida');
}