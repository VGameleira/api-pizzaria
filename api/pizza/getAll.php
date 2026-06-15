<?php

/**
 * API REST - Listar pizzas com paginação
 * 
 * Método HTTP: GET
 * Parâmetros (opcionais): ?page=1&limit=10
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

    $pizza = new Pizza($db);

    $page  = max(1, (int) ($_GET['page'] ?? 1));
    $limit = max(1, min(100, (int) ($_GET['limit'] ?? 10)));
    $total = $pizza->count();
    $stmt  = $pizza->readPaginated($page, $limit);

    $dados = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dados[] = [
            'idPizza'      => (int) $row['idPizza'],
            'nome'         => $row['nome'],
            'ingredientes' => $row['ingredientes'],
            'valor'        => (float) $row['valor'],
        ];
    }

    Response::success([
        'paginacao' => [
            'pagina_atual'    => $page,
            'itens_por_pagina'=> $limit,
            'total_itens'     => (int) $total,
            'total_paginas'   => (int) ceil($total / $limit),
        ],
        'dados' => $dados,
    ]);
} catch (Exception $e) {
    Response::error(500, 'Erro ao buscar pizzas');
}
    
