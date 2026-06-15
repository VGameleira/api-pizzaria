<?php

/**
 * Gerencia a conexão com o banco de dados usando PDO.
 * 
 * As credenciais são carregadas de variáveis de ambiente (.env)
 * para evitar exposição de dados sensíveis no código-fonte.
 */
class Database
{
    private string $host;
    private string $dbName;
    private string $username;
    private string $password;
    private string $port;
    private string $charset;

    public ?PDO $conn = null;

    public function __construct()
    {
        // Carrega as configurações do ambiente, com fallback para valores padrão
        $this->host     = getenv('DB_HOST')     ?: 'localhost';
        $this->dbName   = getenv('DB_NAME')     ?: 'jucapizzadb';
        $this->username = getenv('DB_USER')     ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->port     = getenv('DB_PORT')     ?: '3306';
        $this->charset  = getenv('DB_CHARSET')  ?: 'utf8mb4';
    }

    /**
     * Retorna a conexão PDO ativa, criando uma nova se necessário.
     *
     * @return PDO|null Retorna a instância PDO ou null em caso de falha.
     */
    public function getConnection(): ?PDO
    {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $this->host,
                $this->port,
                $this->dbName,
                $this->charset
            );

            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            return $this->conn;
        } catch (PDOException $e) {
            error_log('Erro de conexão com o banco: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Encerra a conexão explicitamente.
     */
    public function close(): void
    {
        $this->conn = null;
    }
}