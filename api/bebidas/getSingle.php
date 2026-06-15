<?php
// API REST - Obter uma única bebida pelo ID
// Método HTTP: GET
// Parâmetro: idBebida (obrigatório)

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
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Erro ao conectar ao banco de dados"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Instanciar o objeto Bebida
    $bebida = new Bebida($db);

    // Validar e obter o ID da bebida
    if (!isset($_GET['idBebida']) || empty($_GET['idBebida'])) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "ID da bebida não fornecido"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $bebida->idBebida = (int)$_GET['idBebida'];

    // Buscar a bebida
    if ($bebida->read_single()) {
        // Bebida encontrada
        $bebida_arr = array(
            "sucesso" => true,
            "dados" => array(
                "idBebida" => (int)$bebida->idBebida,
                "nome" => $bebida->nome,
                "alcoolica" => (int)$bebida->alcoolica,
                "valor" => (float)$bebida->valor
            )
        );

        http_response_code(200);
        echo json_encode($bebida_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // Bebida não encontrada
        http_response_code(404);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Bebida não encontrada"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    // Tratamento de erro
    http_response_code(500);
    echo json_encode(array(
        "sucesso" => false,
        "mensagem" => "Erro ao buscar bebida",
        "erro" => $e->getMessage()
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>