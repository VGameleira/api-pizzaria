<?php
class Pizza
{
    private $conn;
    private $tabela = "pizzas";

    public $idPizza;
    public $nome;
    public $ingredientes;
    public $valor;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Retorna todas as pizzas ordenadas por valor
    function read()
    {
        $query = "SELECT idPizza, nome, ingredientes, valor FROM " . $this->tabela . " ORDER BY valor";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Retorna pizzas com paginação
    public function read_paginated($page = 1, $limit = 10)
    {
        // Calcular o offset baseado na página
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT idPizza, nome, ingredientes, valor FROM " . $this->tabela . " ORDER BY valor LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Retorna o total de pizzas
    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->tabela;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Busca uma única pizza pelo ID
    public function read_single()
    {
        $query = 'SELECT
                    p.idPizza,
                    p.nome,
                    p.ingredientes,
                    p.valor
                FROM
                    ' . $this->tabela . ' p
                WHERE
                    p.idPizza = ?
                LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->idPizza);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se encontrou registro
        if ($row) {
            $this->nome = $row['nome'];
            $this->ingredientes = $row['ingredientes'];
            $this->valor = $row['valor'];
            return true;
        }
        return false;
    }

    // Insere nova pizza
    public function create()
    {
        $query = 'INSERT INTO ' . $this->tabela . ' SET nome = :nome, ingredientes = :ingredientes, valor = :valor';
        $stmt = $this->conn->prepare($query);

        // Limpeza dos dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->ingredientes = htmlspecialchars(strip_tags($this->ingredientes));
        $this->valor = htmlspecialchars(strip_tags($this->valor));

        // Vinculação
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':ingredientes', $this->ingredientes);
        $stmt->bindParam(':valor', $this->valor);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Atualiza uma pizza existente
    public function update()
    {
        $query = 'UPDATE ' . $this->tabela . ' SET nome = :nome, ingredientes = :ingredientes, valor = :valor WHERE idPizza = :idPizza';
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->ingredientes = htmlspecialchars(strip_tags($this->ingredientes));
        $this->valor = htmlspecialchars(strip_tags($this->valor));
        $this->idPizza = htmlspecialchars(strip_tags($this->idPizza));

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':ingredientes', $this->ingredientes);
        $stmt->bindParam(':valor', $this->valor);
        $stmt->bindParam(':idPizza', $this->idPizza);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Exclui uma pizza
    public function delete()
    {
        $query = 'DELETE FROM ' . $this->tabela . ' WHERE idPizza = :idPizza';
        $stmt = $this->conn->prepare($query);

        $this->idPizza = htmlspecialchars(strip_tags($this->idPizza));
        $stmt->bindParam(':idPizza', $this->idPizza);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
}
?>