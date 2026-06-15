<?php

/**
 * Modelo da entidade Pizza.
 * 
 * Encapsula as operações de banco de dados relacionadas a pizzas,
 * utilizando prepared statements do PDO para proteção contra SQL injection.
 */
class Pizza
{
    private PDO $conn;
    private string $tabela = 'pizzas';

    public int    $idPizza;
    public string $nome;
    public string $ingredientes;
    public float  $valor;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Lista todas as pizzas ordenadas por valor.
     */
    public function read(): PDOStatement
    {
        $query = "SELECT idPizza, nome, ingredientes, valor
                  FROM {$this->tabela}
                  ORDER BY valor";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lista pizzas com paginação.
     */
    public function readPaginated(int $page = 1, int $limit = 10): PDOStatement
    {
        $offset = ($page - 1) * $limit;

        $query = "SELECT idPizza, nome, ingredientes, valor
                  FROM {$this->tabela}
                  ORDER BY valor
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Retorna o total de registros na tabela.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM {$this->tabela}";
        $stmt = $this->conn->query($query);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca uma pizza pelo ID e preenche as propriedades da instância.
     */
    public function readSingle(): bool
    {
        $query = "SELECT idPizza, nome, ingredientes, valor
                  FROM {$this->tabela}
                  WHERE idPizza = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->idPizza, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $this->nome         = $row['nome'];
        $this->ingredientes = $row['ingredientes'];
        $this->valor        = (float) $row['valor'];
        return true;
    }

    /**
     * Insere uma nova pizza no banco.
     */
    public function create(): bool
    {
        $query = "INSERT INTO {$this->tabela} (nome, ingredientes, valor)
                  VALUES (:nome, :ingredientes, :valor)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nome',         $this->sanitizeText($this->nome));
        $stmt->bindValue(':ingredientes', $this->sanitizeText($this->ingredientes));
        $stmt->bindValue(':valor',        $this->valor);

        return $stmt->execute();
    }

    /**
     * Atualiza uma pizza existente.
     */
    public function update(): bool
    {
        $query = "UPDATE {$this->tabela}
                  SET nome = :nome, ingredientes = :ingredientes, valor = :valor
                  WHERE idPizza = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nome',         $this->sanitizeText($this->nome));
        $stmt->bindValue(':ingredientes', $this->sanitizeText($this->ingredientes));
        $stmt->bindValue(':valor',        $this->valor);
        $stmt->bindValue(':id',           $this->idPizza, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Exclui uma pizza pelo ID.
     */
    public function delete(): bool
    {
        $query = "DELETE FROM {$this->tabela} WHERE idPizza = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->idPizza, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Retorna os dados formatados para resposta JSON.
     */
    public function toArray(): array
    {
        return [
            'idPizza'      => $this->idPizza,
            'nome'         => $this->nome,
            'ingredientes' => $this->ingredientes,
            'valor'        => $this->valor,
        ];
    }

    /**
     * Sanitiza texto removendo tags HTML e caracteres especiais.
     */
    private function sanitizeText(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}