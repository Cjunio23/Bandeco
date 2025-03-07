<?php
session_start();
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_cpf"])) {
    header("Location: " . route('login'));
    exit();
}

$user_cpf = $_SESSION["user_cpf"];

function gerarCodigoPix($valor, $cpf) {
    return strtoupper(md5(uniqid($valor . $cpf, true)));
}

$codigo_pix = null;
$valor = null;
$saldo_atual = 0;

// Buscar o saldo atual do usuário
$stmt = $pdo->prepare("SELECT saldo FROM usuario WHERE CPF = :cpf");
$stmt->bindParam(":cpf", $user_cpf);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
if ($usuario) {
    $saldo_atual = $usuario['saldo'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $valor = floatval($_POST['valor']);
    
    if ($valor > 0) {
        $codigo_pix = gerarCodigoPix($valor, $user_cpf);

        try {
            $pdo->beginTransaction();

            // Inserir a recarga no banco de dados e confirmar pagamento imediatamente
            $stmt = $pdo->prepare("INSERT INTO recargas (cpf_usuario, valor, metodo_pagamento, data) VALUES (:cpf, :valor, 'PIX', NOW())");
            $stmt->bindParam(":cpf", $user_cpf);
            $stmt->bindParam(":valor", $valor);
            $stmt->execute();

            // Atualizar o saldo do usuário
            $stmt = $pdo->prepare("UPDATE usuario SET saldo = saldo + :valor WHERE CPF = :cpf");
            $stmt->bindParam(":cpf", $user_cpf);
            $stmt->bindParam(":valor", $valor);
            $stmt->execute();

            $pdo->commit();
            
            // Atualizar saldo exibido na página
            $saldo_atual += $valor;
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erro ao registrar recarga: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recargas - Sistema de Alimentação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Bandeco</h1>
            <ul class="flex space-x-4">
                <li><a href="<?= route('dashboard') ?>" class="text-white hover:underline">Home</a></li>
                <li><a href="<?= route('transacoes') ?>" class="text-white hover:underline">Transações</a></li>
                <li><a href="<?= route('logout') ?>" class="text-white hover:underline">Sair</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto mt-10 max-w-md bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-blue-600">Fazer Recarga</h2>
        
        <div class="text-center text-lg font-semibold mt-2">Saldo Atual: R$ <?= number_format($saldo_atual, 2, ',', '.') ?></div>
        
        <form method="POST" class="mt-4">
            <label class="block text-sm">Valor</label>
            <input type="number" name="valor" step="0.01" required class="w-full p-2 border rounded">
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mt-4">Gerar Código PIX</button>
        </form>
        
        <?php if ($codigo_pix): ?>
            <div class="mt-6 p-4 bg-green-100 rounded">
                <h3 class="text-green-700 font-bold">Código PIX Gerado:</h3>
                <p class="text-gray-700 font-mono break-words"><?= $codigo_pix ?></p>
                <p class="text-sm text-gray-500 mt-2">Recarga confirmada! Saldo atualizado.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
