<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

$senhaNutri = password_hash('1234', PASSWORD_DEFAULT);
$senhaAdm = password_hash('1234', PASSWORD_DEFAULT);

$pdo->exec("INSERT INTO usuario (nome, cpf, email, senha, role) 
VALUES ('Carol', '12345678912', 'nutri@gmail.com', '$senhaNutri', 'nutricionista')");

$pdo->exec("INSERT INTO usuario (nome, cpf, email, senha, role) 
VALUES ('Raimundo', '09876543211', 'adm@gmail.com', '$senhaAdm', 'admin')");

echo "Usuários criados com sucesso!";
