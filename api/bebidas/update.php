<?php
// API REST - Atualizar uma bebida
// Método HTTP: PUT
// Corpo da requisição (JSON): {id, nome, alcoolica, valor}

// Configurar headers CORS e Content-Type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Incluir arquivos de banco de dados e modelo
include_once '../../config/Database.php';
include_once '../../models/Bebida.php';

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

    // Instanciar o objeto Bebida
    $bebida = new Bebida($db);

    // Verificar se o método HTTP é PUT
    if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
        http_response_code(405);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Método HTTP não permitido. Use PUT."
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Obter os dados do corpo da requisição
    $data = json_decode(file_get_contents("php://input"));

    // Validar se os dados não estão vazios
    if (
        empty($data->id) ||
        empty($data->nome) ||
        !isset($data->alcoolica) ||
        empty($data->valor)
    ) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Dados incompletos. Forneça: id, nome, alcoolica (0 ou 1), valor"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Atribuir o ID para atualização
    $bebida->idBebida = (int)$data->id;

    // Atribuir os demais valores
    $bebida->nome = trim($data->nome);
    $bebida->alcoolica = (int)$data->alcoolica;
    $bebida->valor = (float)$data->valor;

    // Validar tipos de dados
    if (strlen($bebida->nome) < 3 || strlen($bebida->nome) > 50) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Nome deve ter entre 3 e 50 caracteres"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($bebida->alcoolica !== 0 && $bebida->alcoolica !== 1) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "alcoolica deve ser 0 (não alcoólica) ou 1 (alcoólica)"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($bebida->valor <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Valor deve ser maior que zero"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Tentar atualizar a bebida
    if ($bebida->update()) {
        http_response_code(200);
        echo json_encode(array(
            "sucesso" => true,
            "mensagem" => "Bebida atualizada com sucesso"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(array(
            "sucesso" => false,
            "mensagem" => "Não foi possível atualizar a bebida"
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar bebida",
        "erro" => $e->getMessage()
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>