<?php 
class Bebida{
    private $conn;
    private $tabela = "bebidas";

    public $idBebida;
    public $nome;
    public $alcoolica;
    public $valor;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lista todas as bebidas ordenadas por valor
    function read() {
        $query = "SELECT * FROM " . $this->tabela . " ORDER BY valor";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lista bebidas com paginação
    public function read_paginated($page = 1, $limit = 10) {
        // Calcular o offset baseado na página
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT idBebida, nome, alcoolica, valor FROM " . $this->tabela . " ORDER BY valor LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Retorna o total de bebidas
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->tabela;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Busca uma bebida pelo ID
    public function read_single() {
        $query = 'SELECT
                    b.idBebida,
                    b.nome,
                    b.alcoolica,
                    b.valor
                FROM
                    ' . $this->tabela . ' b
                WHERE
                    b.idBebida = ?
                LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->idBebida);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nome = $row['nome'];
            $this->alcoolica = $row['alcoolica'];
            $this->valor = $row['valor'];
            return true;
        }
        return false;
    }

    // Cria nova bebida
    public function create() {
        $query = 'INSERT INTO ' . $this->tabela . ' SET nome = :nome, alcoolica = :alcoolica, valor = :valor';
        $stmt = $this->conn->prepare($query);
        
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        // alcoolica é tratado como inteiro (0 ou 1)
        $this->alcoolica = (int) $this->alcoolica;
        $this->valor = htmlspecialchars(strip_tags($this->valor));

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':alcoolica', $this->alcoolica);
        $stmt->bindParam(':valor', $this->valor);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Atualiza bebida existente
    public function update() {
        $query = 'UPDATE ' . $this->tabela . ' SET nome = :nome, alcoolica = :alcoolica, valor = :valor WHERE idBebida = :id';
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->alcoolica = (int) $this->alcoolica;
        $this->valor = htmlspecialchars(strip_tags($this->valor));
        $this->idBebida = htmlspecialchars(strip_tags($this->idBebida));

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':alcoolica', $this->alcoolica);
        $stmt->bindParam(':valor', $this->valor);
        $stmt->bindParam(':id', $this->idBebida);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Exclui bebida
    public function delete() {
        $query = 'DELETE FROM ' . $this->tabela . ' WHERE idBebida = :id';
        $stmt = $this->conn->prepare($query);

        $this->idBebida = htmlspecialchars(strip_tags($this->idBebida));
        $stmt->bindParam(':id', $this->idBebida);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
}
?>