<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../public/middleware.php';
verificarPermissao('admin');

// Verifica se o formulário de pesquisa foi enviado
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Configurações de paginação
$itensPorPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Consulta para listar usuários com base na pesquisa e paginação
$query = "SELECT CPF, Nome, Saldo FROM usuario";
if ($search) {
    $query .= " WHERE Nome LIKE :search";
}
$query .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para contar o número total de usuários (para calcular o número de páginas)
$totalQuery = "SELECT COUNT(*) FROM usuario";
if ($search) {
    $totalQuery .= " WHERE Nome LIKE :search";
}
$totalStmt = $pdo->prepare($totalQuery);
if ($search) {
    $totalStmt->bindValue(':search', '%' . $search . '%');
}
$totalStmt->execute();
$totalUsuarios = $totalStmt->fetchColumn();
$totalPaginas = ceil($totalUsuarios / $itensPorPagina);

// Consulta para listar movimentações de saldo (recargas e transações), limitando o número de linhas
$movimentacoesStmt = $pdo->query("SELECT r.cpf_usuario, r.data, r.valor AS valor_recarga, t.valor AS valor_transacao, t.tipo 
                                  FROM recargas r 
                                  LEFT JOIN transacoes t ON r.cpf_usuario = t.cpf_origem OR r.cpf_usuario = t.cpf_destino
                                  ORDER BY r.data DESC LIMIT 10"); // Limite de 10 movimentações
$movimentacoes = $movimentacoesStmt->fetchAll(PDO::FETCH_ASSOC);

// Função para excluir usuário
if (isset($_GET['excluir'])) {
    $cpfExcluir = $_GET['excluir'];

    // Excluir transações e recargas associadas ao usuário
    $pdo->beginTransaction();
    try {
        $pdo->prepare("DELETE FROM transacoes WHERE cpf_origem = :cpf OR cpf_destino = :cpf")->execute([':cpf' => $cpfExcluir]);
        $pdo->prepare("DELETE FROM recargas WHERE cpf_usuario = :cpf")->execute([':cpf' => $cpfExcluir]);
        // Excluir o usuário
        $pdo->prepare("DELETE FROM usuario WHERE CPF = :cpf")->execute([':cpf' => $cpfExcluir]);
        $pdo->commit();
        header("Location: gerenciar_usuarios.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro ao excluir usuário: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Bandeco - Admin</h1>
            <ul class="flex space-x-4">
                <li><a href="../public/logout.php" class="text-white hover:underline">Sair</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-center">Gerenciamento de Usuários</h2>

        <!-- Formulário de pesquisa -->
        <form action="" method="get" class="mb-6 flex justify-center">
            <input type="text" name="search" placeholder="Pesquisar por nome" value="<?= htmlspecialchars($search) ?>" class="p-2 border rounded-l-lg">
            <button type="submit" class="bg-blue-600 text-white p-2 rounded-r-lg">Pesquisar</button>
        </form>

        <!-- Tabela de usuários -->
        <table class="mt-6 w-full bg-white shadow-md rounded-lg">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="p-2">CPF</th>
                    <th class="p-2">Nome</th>
                    <th class="p-2">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($usuario['CPF']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($usuario['Nome']) ?></td>
                        <td class="p-2">R$ <?= number_format($usuario['Saldo'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botões de navegação -->
        <div class="flex justify-between mt-6">
            <?php if ($paginaAtual > 1): ?>
                <a href="?pagina=<?= $paginaAtual - 1 ?>&search=<?= htmlspecialchars($search) ?>" class="bg-blue-600 text-white p-2 rounded-lg">Voltar</a>
            <?php endif; ?>
            <?php if ($paginaAtual < $totalPaginas): ?>
                <a href="?pagina=<?= $paginaAtual + 1 ?>&search=<?= htmlspecialchars($search) ?>" class="bg-blue-600 text-white p-2 rounded-lg">Próxima Página</a>
            <?php endif; ?>
        </div>

        <!-- Tabela de movimentação de saldo -->
        <h3 class="text-2xl font-bold mt-6">Movimentação de Saldo</h3>
        <table class="mt-4 w-full bg-white shadow-md rounded-lg">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="p-2">CPF Usuário</th>
                    <th class="p-2">Data</th>
                    <th class="p-2">Valor (Recarga)</th>
                    <th class="p-2">Valor (Transação)</th>
                    <th class="p-2">Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentacoes as $movimentacao): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($movimentacao['cpf_usuario']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($movimentacao['data']) ?></td>
                        <td class="p-2">R$ <?= number_format($movimentacao['valor_recarga'], 2, ',', '.') ?></td>
                        <td class="p-2">R$ <?= number_format($movimentacao['valor_transacao'], 2, ',', '.') ?></td>
                        <td class="p-2"><?= htmlspecialchars($movimentacao['tipo']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botões de navegação para movimentação -->
        <div class="flex justify-between mt-6">
            <?php if ($paginaAtual > 1): ?>
                <a href="?pagina=<?= $paginaAtual - 1 ?>&search=<?= htmlspecialchars($search) ?>" class="bg-blue-600 text-white p-2 rounded-lg">Voltar</a>
            <?php endif; ?>
            <?php if ($paginaAtual < $totalPaginas): ?>
                <a href="?pagina=<?= $paginaAtual + 1 ?>&search=<?= htmlspecialchars($search) ?>" class="bg-blue-600 text-white p-2 rounded-lg">Próxima Página</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
