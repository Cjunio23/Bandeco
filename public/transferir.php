<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_cpf"])) {
    header("Location: " . route('login'));
    exit();
}

$user_cpf = $_SESSION["user_cpf"];

// Verifica se os campos foram preenchidos
if (!isset($_POST['cpf_destino']) || !isset($_POST['valor'])) {
    die("Preencha todos os campos.");
}

$cpf_destino = trim($_POST['cpf_destino']);
$valor = floatval($_POST['valor']);

if ($valor <= 0) {
    die("Valor inválido.");
}

try {
    $pdo->beginTransaction();

    // Verifica saldo do usuário
    $stmt = $pdo->prepare("SELECT saldo FROM usuario WHERE cpf = ?");
    $stmt->execute([$user_cpf]);
    $saldo_origem = $stmt->fetchColumn();

    if ($saldo_origem === false) {
        die("Erro: Usuário não encontrado.");
    }

    if ($saldo_origem < $valor) {
        die("Saldo insuficiente.");
    }

    // Verifica se o destinatário existe
    $stmt = $pdo->prepare("SELECT saldo FROM usuario WHERE cpf = ?");
    $stmt->execute([$cpf_destino]);
    $destinatario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$destinatario) {
        die("Erro: CPF do destinatário não encontrado.");
    }

    // Debita do remetente
    $stmt = $pdo->prepare("UPDATE usuario SET saldo = saldo - ? WHERE cpf = ?");
    $stmt->execute([$valor, $user_cpf]);

    // Credita no destinatário
    $stmt = $pdo->prepare("UPDATE usuario SET saldo = saldo + ? WHERE cpf = ?");
    $stmt->execute([$valor, $cpf_destino]);

    // Registra a transação
    $stmt = $pdo->prepare("INSERT INTO transacoes (cpf_origem, cpf_destino, valor, data, tipo) VALUES (?, ?, ?, NOW(), 'envio')");
    $stmt->execute([$user_cpf, $cpf_destino, $valor]);

    $stmt = $pdo->prepare("INSERT INTO transacoes (cpf_origem, cpf_destino, valor, data, tipo) VALUES (?, ?, ?, NOW(), 'recebimento')");
    $stmt->execute([$cpf_destino, $user_cpf, $valor]);

    $pdo->commit();
    
    header("Location: transacoes.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao processar a transação: " . $e->getMessage());
}
?>
