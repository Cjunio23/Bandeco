<?php
class Database {
    private $host = "localhost";
    private $db_name = "restaurante";
    private $username = "root";
    private $password = "0987";
    private $conn;

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                die("Erro de conexão: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }
}

// Criar instância de conexão
$db = new Database();
$pdo = $db->getConnection();
?>
