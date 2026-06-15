<?php
// API REST - Criar uma nova pizza
// Método HTTP: POST
// Corpo da requisição (JSON): {nome, ingredientes, valor}

// Configurar headers CORS e Content-Type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

    // Verificar se o método HTTP é POST
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(405);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Método HTTP não permitido. Use POST."
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obter os dados do corpo da requisição
    $data = json_decode(file_get_contents("php://input"));

    // Validar se os dados não estão vazios
    if (
        empty($data->nome) ||
        empty($data->ingredientes) ||
        empty($data->valor)
    ) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Dados incompletos. Forneça: nome, ingredientes, valor"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Atribuir os valores ao objeto Pizza
    $pizza->nome = trim($data->nome);
    $pizza->ingredientes = trim($data->ingredientes);
    $pizza->valor = (float)$data->valor;

    // Validar tipos de dados
    if (strlen($pizza->nome) < 3 || strlen($pizza->nome) > 50) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Nome deve ter entre 3 e 50 caracteres"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($pizza->ingredientes) < 5 || strlen($pizza->ingredientes) > 255) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Ingredientes devem ter entre 5 e 255 caracteres"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($pizza->valor <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Valor deve ser maior que zero"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Criar a pizza
    if ($pizza->create()) {
        http_response_code(201);
        echo json_encode(array(
            "sucesso" => true,
            "mensagem" => "Pizza criada com sucesso"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Não foi possível criar a pizza"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "sucesso" => false,
        "mensagem" => "Erro ao criar pizza",
        "erro" => $e->getMessage()
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>

