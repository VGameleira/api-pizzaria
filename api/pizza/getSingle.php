<?php

/**
 * API REST - Obter uma única pizza pelo ID
 * 
 * Método HTTP: GET
 * Parâmetro query: ?idPizza=1
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Response.php';
require_once __DIR__ . '/../../models/Pizza.php';

Response::setupCORS('GET, OPTIONS');
Response::requireMethod('GET');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        Response::error(500, 'Erro ao conectar ao banco de dados');
    }

    if (!isset($_GET['idPizza']) || $_GET['idPizza'] === '') {
        Response::error(400, 'ID da pizza não fornecido');
    }

    $pizza = new Pizza($db);
    $pizza->idPizza = (int) $_GET['idPizza'];

    if (!$pizza->readSingle()) {
        Response::error(404, 'Pizza não encontrada');
    }

    Response::success($pizza->toArray());
} catch (Exception $e) {
    Response::error(500, 'Erro ao buscar pizza');
}