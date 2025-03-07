<?php

class CardapioController {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function listarCardapios() {
        $query = "SELECT * FROM cardapios";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function criarCardapio($nome, $descricao) {
        $query = "INSERT INTO cardapios (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        return $stmt->execute();
    }
    
    public function atualizarCardapio($id, $nome, $descricao) {
        $query = "UPDATE cardapios SET nome = :nome, descricao = :descricao WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        return $stmt->execute();
    }
    
    public function deletarCardapio($id) {
        $query = "DELETE FROM cardapios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
