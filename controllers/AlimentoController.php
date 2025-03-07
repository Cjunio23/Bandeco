<?php

class AlimentoController {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function listarAlimentos() {
        $query = "SELECT * FROM alimentos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function criarAlimento($nome, $calorias) {
        $query = "INSERT INTO alimentos (nome, calorias) VALUES (:nome, :calorias)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':calorias', $calorias);
        return $stmt->execute();
    }
    
    public function atualizarAlimento($id, $nome, $calorias) {
        $query = "UPDATE alimentos SET nome = :nome, calorias = :calorias WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':calorias', $calorias);
        return $stmt->execute();
    }
    
    public function deletarAlimento($id) {
        $query = "DELETE FROM alimentos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
