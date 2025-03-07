<?php

class AdministradorController {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function listarAdministradores() {
        $query = "SELECT * FROM administradores";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function criarAdministrador($nome, $email, $senha) {
        $query = "INSERT INTO administradores (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', password_hash($senha, PASSWORD_DEFAULT));
        return $stmt->execute();
    }
    
    public function atualizarAdministrador($id, $nome, $email, $senha) {
        $query = "UPDATE administradores SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', password_hash($senha, PASSWORD_DEFAULT));
        return $stmt->execute();
    }
    
    public function deletarAdministrador($id) {
        $query = "DELETE FROM administradores WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
