<?php

namespace App\Controllers;

use PDO;
use App\Config\Database;

class UsuarioController {
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function listar() {
        $stmt = $this->pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($cpf) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE cpf = :cpf");
        $stmt->bindParam(":cpd", $cpf);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
        $stmt->execute([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT)
        ]);
        return $this->pdo->lastInsertId();
    }

    public function atualizar($cpf, $dados) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE cpf = cpf");
        return $stmt->execute([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
            'cpf' => $cpf
        ]);
    }

    public function deletar($cpf) {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE cpf = :cpf");
        return $stmt->execute(['cpf' => $cpf]);
    }
}
