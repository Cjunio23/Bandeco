<?php

class RecargaController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listarRecargas() {
        $query = "SELECT * FROM recargas";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarRecarga($id) {
        $query = "SELECT * FROM recargas WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarRecarga($usuario_id, $valor) {
        $query = "INSERT INTO recargas (usuario_id, valor, data_recarga) VALUES (:usuario_id, :valor, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function atualizarRecarga($id, $valor) {
        $query = "UPDATE recargas SET valor = :valor WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function excluirRecarga($id) {
        $query = "DELETE FROM recargas WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
