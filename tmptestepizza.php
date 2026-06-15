<?php 
require_once 'config/Database.php';
require_once 'models/Pizza.php'; // Incluímos nossa nova classe
 
echo "<h1>Testando Conexão e Modelo</h1>";
 
$database = new Database();
$db = $database->getConnection();
 
if (!$db) {
    echo "<p style='color: red;'>Falha na conexão.</p>";
    die(); // Encerra o script se não houver conexão
}
 
echo "<p style='color: green;'>Conexão bem-sucedida!</p>";
 
echo "<h2>Criando um objeto Pizza...</h2>";
 
// Criamos uma instância da classe Pizza, passando a conexão com o banco
$pizza = new Pizza($db);
 
// Atribuímos valores às suas propriedades públicas
$pizza->nome = 'Margherita';
$pizza->ingredientes = 'Mussarela, fatias de tomate e manjericão fresco';
$pizza->valor = 42.50;
 
// Vamos inspecionar o objeto!
echo "<pre>"; // A tag <pre> ajuda a formatar a saída do print_r
print_r($pizza);
echo "</pre>";