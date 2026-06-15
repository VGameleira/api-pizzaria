<?php
 
require_once 'config/Database.php';
 
echo "<h1>Testando Conexão com o Banco de Dados</h1>";
 
$database = new Database();
$conn = $database->getConnection();
 
if ($conn) {
    echo "<p style='color: green;'>Conexão bem-sucedida!</p>";
} else {
    echo "<p style='color: red;'>Falha na conexão. Verifique as credenciais no arquivo config/Database.php e se o banco de dados 'pizzaria_db' existe.</p>";
}
?>