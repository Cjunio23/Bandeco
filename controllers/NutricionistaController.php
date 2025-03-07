<?php

namespace App\Controllers;

use PDO;
use PDOException;
use Config\Database;

class NutricionistaController {
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->getConnection();
    }

    public function listar() {
        $sql = "SELECT * FROM nutricionistas";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM nutricionistas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $sql = "INSERT INTO nutricionistas (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dados['nome'], $dados['email'], password_hash($dados['senha'], PASSWORD_BCRYPT)]);
    }

    public function atualizar($id, $dados) {
        $sql = "UPDATE nutricionistas SET nome = ?, email = ?, senha = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dados['nome'], $dados['email'], password_hash($dados['senha'], PASSWORD_BCRYPT), $id]);
    }

    public function deletar($id) {
        $sql = "DELETE FROM nutricionistas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
