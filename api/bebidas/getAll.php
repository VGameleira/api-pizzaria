<?php
// API REST - Listar todas as bebidas com paginação
// Método HTTP: GET
// Parâmetros: page (padrão 1), limit (padrão 10)

// Configurar headers CORS e Content-Type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Incluir arquivos de banco de dados e modelo
include_once '../../config/Database.php';
include_once '../../models/Bebida.php';

try {
    // Instanciar o objeto Database e obter a conexão
    $database = new Database();
    $db = $database->getConnection();

    // Verificar se a conexão foi estabelecida
    if (!$db) {
        http_response_code(500);
        echo json_encode(array("mensagem" => "Erro ao conectar ao banco de dados"));
        exit;
    }

    // Instanciar o objeto Bebida
    $bebida = new Bebida($db);

    // Validar e obter parâmetros de paginação
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;

    // Obter o total de registros
    $total = $bebida->count();

    // Chamar o método com paginação
    $stmt = $bebida->read_paginated($page, $limit);
    $num = $stmt->rowCount();

    // Calcular informações de paginação
    $total_pages = ceil($total / $limit);

    // Verificar se foram encontrados registros
    if ($num > 0) {
        // Array para armazenar as bebidas
        $bebidas_arr = array();

        // Percorrer os resultados
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $bebida_item = array(
                "idBebida" => (int)$idBebida,
                "nome" => $nome,
                "alcoolica" => (int)$alcoolica,
                "valor" => (float)$valor
            );

            array_push($bebidas_arr, $bebida_item);
        }

        // Preparar resposta com dados de paginação
        $response = array(
            "sucesso" => true,
            "paginacao" => array(
                "pagina_atual" => $page,
                "itens_por_pagina" => $limit,
                "total_itens" => (int)$total,
                "total_paginas" => (int)$total_pages
            ),
            "dados" => $bebidas_arr
        );

        http_response_code(200);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // Nenhuma bebida encontrada
        http_response_code(200);
        echo json_encode(array(
            "sucesso" => true,
            "mensagem" => "Nenhuma bebida encontrada",
            "dados" => array()
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    // Tratamento de erro
    http_response_code(500);
    echo json_encode(array(
        "sucesso" => false,
        "mensagem" => "Erro ao buscar bebidas",
        "erro" => $e->getMessage()
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>

