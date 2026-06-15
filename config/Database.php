<?php
class Database{
    private $host = 'localhost';
    private $db_name = 'jucapizzadb';
    private $username = 'root';
    private $password = 'usbw';
    private $port = '3311';
 
    public $conn;
 
    public function getConnection()
    {
        $this->conn = null;
 
        try {
            // DSN (Data Source Name) - String de conexão
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8';
 
            // Instancia o objeto PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);
 
            // Define o modo de erro do PDO para exceção
            // Isso faz com que o PDO lance exceções em caso de erros, facilitando o tratamento
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
        } catch (PDOException $e) {
            // Em caso de erro na conexão, exibe a mensagem de erro
            echo 'Erro de Conexão: ' . $e->getMessage();
        }
 
        return $this->conn;
    }
}
?>