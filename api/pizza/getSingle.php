<?php
// API REST - Obter uma única pizza pelo ID
// Método HTTP: GET
// Parâmetro: idPizza (obrigatório)

// Configurar headers CORS e Content-Type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Incluir arquivos de banco de dados e modelo
include_once '../../config/Database.php';
include_once '../../models/Pizza.php';

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

    // Instanciar o objeto Pizza
    $pizza = new Pizza($db);

    // Validar e obter o ID da pizza
    if (!isset($_GET['idPizza']) || empty($_GET['idPizza'])) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "ID da pizza não fornecido"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pizza->idPizza = (int)$_GET['idPizza'];

    // Buscar a pizza
    if ($pizza->read_single()) {
        // Pizza encontrada
        $pizza_arr = array(
            "sucesso" => true,
            "dados" => array(
                "idPizza" => (int)$pizza->idPizza,
                "nome" => $pizza->nome,
                "ingredientes" => $pizza->ingredientes,
                "valor" => (float)$pizza->valor
            )
        );

        http_response_code(200);
        echo json_encode($pizza_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // Pizza não encontrada
        http_response_code(404);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Pizza não encontrada"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    // Tratamento de erro
    http_response_code(500);
    echo json_encode(array(
        "sucesso" => false,
        "mensagem" => "Erro ao buscar pizza",
        "erro" => $e->getMessage()
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>