<?php
session_start();
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION["user_cpf"])) {
    header("Location: " . route('login'));
    exit();
}

$user_cpf = $_SESSION["user_cpf"];

// Obtendo o saldo do usuário
$stmt_saldo = $pdo->prepare("SELECT Saldo FROM consumidor WHERE CPF = ?");
$stmt_saldo->execute([$user_cpf]);
$saldo = $stmt_saldo->fetchColumn();

// Obtendo o histórico de transações
$stmt_transacoes = $pdo->prepare("SELECT * FROM transacoes WHERE cpf_origem = ? OR cpf_destino = ? ORDER BY data DESC");
$stmt_transacoes->execute([$user_cpf, $user_cpf]);
$transacoes = $stmt_transacoes->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações - Sistema de Alimentação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function atualizarSaldo() {
            fetch('atualizar_saldo.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('saldo-atual').innerText = "R$ " + data.saldo;
                })
                .catch(error => console.error('Erro ao atualizar saldo:', error));
        }
    </script>
</head>
<body class="bg-gray-100" onload="atualizarSaldo()">
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Bandeco</h1>
            <ul class="flex space-x-4">
                <li><a href="<?= route('dashboard') ?>" class="text-white hover:underline">Home</a></li>
                <li><a href="<?= route('recargas') ?>" class="text-white hover:underline">Recargas</a></li>
                <li><a href="<?= route('logout') ?>" class="text-white hover:underline">Sair</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto mt-10 max-w-md bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-blue-600">Saldo Atual: <span id="saldo-atual">R$ <?= number_format($saldo, 2, ',', '.') ?></span></h2>

        <form action="transferir.php" method="POST" class="mt-4">
            <input type="text" name="cpf_destino" placeholder="CPF do destinatário" class="border p-2 w-full rounded" required>
            <input type="number" step="0.01" name="valor" placeholder="Valor a transferir" class="border p-2 w-full mt-2 rounded" required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2 w-full rounded">Transferir</button>
        </form>

        <h3 class="text-xl font-bold mt-6">Histórico de Transações</h3>
        <ul class="mt-2">
            <?php foreach ($transacoes as $transacao): ?>
                <li class="border-b py-2 flex justify-between">
                    <span>R$ <?= number_format($transacao['valor'], 2, ',', '.') ?></span>
                    <span class="text-gray-500"><?= date('d/m/Y H:i', strtotime($transacao['data'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
