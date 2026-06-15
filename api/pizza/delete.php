<?php
// API REST - Deletar uma pizza
// Método HTTP: DELETE
// Corpo da requisição (JSON): {id}

// Configurar headers CORS e Content-Type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Incluir arquivos de banco de dados e modelo
include_once '../../config/Database.php';
include_once '../../models/Pizza.php';

// Responder a requisições OPTIONS (preflight do CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

    // Verificar se o método HTTP é DELETE
    if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
        http_response_code(405);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Método HTTP não permitido. Use DELETE."
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obter os dados do corpo da requisição
    $data = json_decode(file_get_contents("php://input"));

    // Validar se o ID foi fornecido
    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "ID da pizza é obrigatório"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Atribuir o ID para exclusão
    $pizza->idPizza = (int)$data->id;

    // Validar se o ID é válido
    if ($pizza->idPizza <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "ID da pizza inválido"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Tentar deletar a pizza
    if ($pizza->delete()) {
        http_response_code(200);
        echo json_encode(array(
            "sucesso" => true,
            "mensagem" => "Pizza deletada com sucesso"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Não foi possível deletar a pizza. Verifique se o ID existe."
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "sucesso" => false,
        "mensagem" => "Erro ao deletar pizza",
        "erro" => $e->getMessage()
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
