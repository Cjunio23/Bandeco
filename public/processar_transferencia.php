<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_cpf"])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
    exit();
}

$user_cpf = $_SESSION["user_cpf"];
$cpf_destino = $_POST['cpf_destino'];
$valor = floatval($_POST['valor']);

// Verificar saldo do remetente
$stmt = $pdo->prepare("SELECT Saldo FROM consumidor WHERE CPF = ?");
$stmt->execute([$user_cpf]);
$saldoAtual = $stmt->fetchColumn();

if ($saldoAtual === false) {
    echo json_encode(["success" => false, "message" => "Usuário remetente não encontrado."]);
    exit();
}

if ($saldoAtual < $valor) {
    echo json_encode(["success" => false, "message" => "Saldo insuficiente."]);
    exit();
}

// Verificar se o destinatário existe
$stmt = $pdo->prepare("SELECT CPF FROM consumidor WHERE CPF = ?");
$stmt->execute([$cpf_destino]);
if ($stmt->fetchColumn() === false) {
    echo json_encode(["success" => false, "message" => "Destinatário não encontrado."]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Atualizar saldo do remetente
    $stmt = $pdo->prepare("UPDATE consumidor SET Saldo = Saldo - ? WHERE CPF = ?");
    $stmt->execute([$valor, $user_cpf]);

    // Atualizar saldo do destinatário
    $stmt = $pdo->prepare("UPDATE consumidor SET Saldo = Saldo + ? WHERE CPF = ?");
    $stmt->execute([$valor, $cpf_destino]);

    // Registrar a transação
    $stmt = $pdo->prepare("INSERT INTO transacoes (cpf_origem, cpf_destino, valor, data, tipo) VALUES (?, ?, ?, NOW(), 'transferencia')");
    $stmt->execute([$user_cpf, $cpf_destino, $valor]);

    $pdo->commit();
    echo json_encode(["success" => true, "message" => "Transferência realizada com sucesso."]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => "Erro ao processar a transferência."]);
}
