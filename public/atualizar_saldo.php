<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_cpf"])) {
    echo json_encode(["saldo" => "Erro"]);
    exit();
}

$user_cpf = $_SESSION["user_cpf"];
$stmt = $pdo->prepare("SELECT saldo FROM usuario WHERE cpf = ?");
$stmt->execute([$user_cpf]);
$saldo = $stmt->fetchColumn();

echo json_encode(["saldo" => number_format($saldo, 2, ',', '.')]);
?>
