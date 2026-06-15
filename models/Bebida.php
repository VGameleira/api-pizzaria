<?php

/**
 * Modelo da entidade Bebida.
 * 
 * Encapsula as operações de banco de dados relacionadas a bebidas,
 * utilizando prepared statements do PDO para proteção contra SQL injection.
 */
class Bebida
{
    private PDO $conn;
    private string $tabela = 'bebidas';

    public int    $idBebida;
    public string $nome;
    public int    $alcoolica; // 0 = não alcoólica, 1 = alcoólica
    public float  $valor;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Lista todas as bebidas ordenadas por valor.
     */
    public function read(): PDOStatement
    {
        $query = "SELECT idBebida, nome, alcoolica, valor
                  FROM {$this->tabela}
                  ORDER BY valor";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lista bebidas com paginação.
     */
    public function readPaginated(int $page = 1, int $limit = 10): PDOStatement
    {
        $offset = ($page - 1) * $limit;

        $query = "SELECT idBebida, nome, alcoolica, valor
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
     * Busca uma bebida pelo ID e preenche as propriedades da instância.
     */
    public function readSingle(): bool
    {
        $query = "SELECT idBebida, nome, alcoolica, valor
                  FROM {$this->tabela}
                  WHERE idBebida = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->idBebida, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $this->nome       = $row['nome'];
        $this->alcoolica  = (int) $row['alcoolica'];
        $this->valor      = (float) $row['valor'];
        return true;
    }

    /**
     * Cria um novo registro de bebida.
     */
    public function create(): bool
    {
        $query = "INSERT INTO {$this->tabela} (nome, alcoolica, valor)
                  VALUES (:nome, :alcoolica, :valor)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nome',       $this->sanitizeText($this->nome));
        $stmt->bindValue(':alcoolica',  $this->alcoolica, PDO::PARAM_INT);
        $stmt->bindValue(':valor',      $this->valor);

        return $stmt->execute();
    }

    /**
     * Atualiza uma bebida existente.
     */
    public function update(): bool
    {
        $query = "UPDATE {$this->tabela}
                  SET nome = :nome, alcoolica = :alcoolica, valor = :valor
                  WHERE idBebida = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nome',       $this->sanitizeText($this->nome));
        $stmt->bindValue(':alcoolica',  $this->alcoolica, PDO::PARAM_INT);
        $stmt->bindValue(':valor',      $this->valor);
        $stmt->bindValue(':id',         $this->idBebida, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Exclui uma bebida pelo ID.
     */
    public function delete(): bool
    {
        $query = "DELETE FROM {$this->tabela} WHERE idBebida = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->idBebida, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Retorna os dados formatados para resposta JSON.
     */
    public function toArray(): array
    {
        return [
            'idBebida'   => $this->idBebida,
            'nome'       => $this->nome,
            'alcoolica'  => $this->alcoolica,
            'valor'      => $this->valor,
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