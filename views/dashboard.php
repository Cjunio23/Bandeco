<?php
session_start();
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../config/database.php'; // Conexão com o banco de dados

if (!isset($_SESSION["user_cpf"])) {
    header("Location: " . route('login'));
    exit();
}

// Conectar ao banco de dados
try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Buscar o cardápio do dia (com a data de hoje)
    $stmt = $pdo->prepare("SELECT * FROM cardapio WHERE Data = CURDATE() LIMIT 1");
    $stmt->execute();
    $cardapio = $stmt->fetch(PDO::FETCH_ASSOC);

    $refeicoes = [];
    if ($cardapio && isset($cardapio['ID_Cardapio'])) {
        // Buscar refeições associadas a esse cardápio
        $sql = "SELECT r.Proteina, r.Carboidrato, r.Salada, r.Acompanhamento
                FROM refeicao r
                JOIN cardapio_refeicao cr ON r.Codigo = cr.Codigo_Refeicao
                WHERE cr.ID_Cardapio = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cardapio['ID_Cardapio']]);
        $refeicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Alimentação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Barra de navegação -->
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">🍽️ Bandeco</h1>
            <ul class="flex space-x-4">
                <li><a href="<?= route('recargas') ?>" class="text-white hover:underline">Recargas</a></li>
                <li><a href="<?= route('transacoes') ?>" class="text-white hover:underline">Transações</a></li>
                <li><a href="<?= route('logout') ?>" class="text-white hover:underline">Sair</a></li>
            </ul>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <div class="container mx-auto mt-10 text-center">
        <h2 class="text-3xl font-bold text-gray-800">🍛 Cardápio do Dia</h2>
        <p class="mt-4 text-lg text-gray-600">Confira as opções disponíveis para hoje!</p>

        <?php if ($cardapio): ?>
            <div class="mt-6 bg-white p-6 shadow-lg rounded-lg w-3/4 mx-auto">
                <h3 class="text-2xl font-semibold text-blue-600">
                    📅 <?= date('d/m/Y', strtotime($cardapio['Data'])) ?>
                </h3>
                <p class="mt-2 text-lg text-gray-700 italic">
                    <?= htmlspecialchars($cardapio['Descricao']) ?>
                </p>

                <?php if (!empty($refeicoes)): ?>
                    <div class="mt-6">
                        <table class="w-full border-collapse border border-gray-300 shadow-md rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-blue-500 text-white">
                                    <th class="py-2 px-4">Proteína</th>
                                    <th class="py-2 px-4">Carboidrato</th>
                                    <th class="py-2 px-4">Salada</th>
                                    <th class="py-2 px-4">Acompanhamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($refeicoes as $refeicao): ?>
                                    <tr class="bg-gray-100 hover:bg-gray-200 transition">
                                        <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($refeicao['Proteina']) ?></td>
                                        <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($refeicao['Carboidrato']) ?></td>
                                        <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($refeicao['Salada']) ?></td>
                                        <td class="py-2 px-4 border border-gray-300"><?= htmlspecialchars($refeicao['Acompanhamento']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mt-4 text-lg text-red-600">Nenhuma refeição cadastrada para este cardápio.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="mt-6 text-lg text-gray-600">❌ Nenhum cardápio disponível para hoje.</p>
        <?php endif; ?>
    </div>

</body>
</html>
