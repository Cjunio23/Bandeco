<?php

namespace App\Controllers;

use PDO;
use App\Config\Database;

class TransacaoController {
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function listar() {
        $stmt = $this->pdo->query("SELECT * FROM transacoes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($cpf) {
        $stmt = $this->pdo->prepare("SELECT * FROM transacoes WHERE cpf = :cpf");
        $stmt->bindParam(":cpf", $cpf);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $stmt = $this->pdo->prepare("INSERT INTO transacoes (cpf, valor, tipo, data) VALUES (:usuario_cpf, :valor, :tipo, :data)");
        $stmt->execute([
            'usuario_cpf' => $dados['usuario_cpf'],
            'valor' => $dados['valor'],
            'tipo' => $dados['tipo'],
            'data' => $dados['data']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function atualizar($cpf, $dados) {
        $stmt = $this->pdo->prepare("UPDATE transacoes SET usuario_cpf = :usuario_cpf, valor = :valor, tipo = :tipo, data = :data WHERE cpf = :cpf");
        return $stmt->execute([
            'usuario_cpf' => $dados['usuario_cpf'],
            'valor' => $dados['valor'],
            'tipo' => $dados['tipo'],
            'data' => $dados['data'],
            'cpf' => $cpf
        ]);
    }

    public function deletar($cpf) {
        $stmt = $this->pdo->prepare("DELETE FROM transacoes WHERE cpf = :cpf");
        return $stmt->execute(['cpf' => $cpf]);
    }
}