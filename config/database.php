<?php
class Database {
    private $host = "xxxxxx";
    private $db_name = "xxxxx";
    private $username = "xxxx";
    private $password = "xxxx";
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
