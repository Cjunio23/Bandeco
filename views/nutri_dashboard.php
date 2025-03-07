<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_cpf'])) {
    die("Erro: CPF do nutricionista não encontrado na sessão.");
}

$cpf_nutricionista = $_SESSION['user_cpf'];
$pdo = new Database();
$conn = $pdo->getConnection();

// Adicionar nova refeição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_refeicao'])) {
    $proteina = $_POST['proteina'];
    $carboidrato = $_POST['carboidrato'];
    $salada = $_POST['salada'];
    $acompanhamento = $_POST['acompanhamento'];

    $stmt = $conn->prepare("INSERT INTO refeicao (Proteina, Carboidrato, Salada, Acompanhamento) VALUES (?, ?, ?, ?)");
    $stmt->execute([$proteina, $carboidrato, $salada, $acompanhamento]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Excluir refeição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_refeicao'])) {
    $refeicao_id = $_POST['refeicao_id'];

    // Excluir das relações antes de deletar a refeição
    $conn->prepare("DELETE FROM cardapio_refeicao WHERE Codigo_Refeicao = ?")->execute([$refeicao_id]);
    $conn->prepare("DELETE FROM refeicao WHERE Codigo = ?")->execute([$refeicao_id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Criar novo cardápio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_cardapio'])) {
    $data = $_POST['data'];
    $descricao = $_POST['descricao'];
    $refeicoes_selecionadas = $_POST['refeicoes'] ?? [];

    try {
        $conn->beginTransaction();

        // Inserir o novo cardápio
        $stmt = $conn->prepare("INSERT INTO cardapio (Data, Descricao) VALUES (?, ?)");
        $stmt->execute([$data, $descricao]);
        $id_cardapio = $conn->lastInsertId();

        // Associar o nutricionista ao cardápio
        $stmt = $conn->prepare("INSERT INTO nutricionista_cria_cardapio (CPF, ID_Cardapio) VALUES (?, ?)");
        $stmt->execute([$cpf_nutricionista, $id_cardapio]);

        // Inserir automaticamente as refeições selecionadas na tabela de associação
        if (!empty($refeicoes_selecionadas)) {
            $stmt = $conn->prepare("INSERT INTO cardapio_refeicao (ID_Cardapio, Codigo_Refeicao) VALUES (?, ?)");
            foreach ($refeicoes_selecionadas as $refeicao) {
                $stmt->execute([$id_cardapio, $refeicao]);
            }
        }

        // Confirmar a transação
        $conn->commit();
    } catch (Exception $e) {
        // Reverter transação em caso de erro
        $conn->rollBack();
        die("Erro ao criar cardápio: " . $e->getMessage());
    }

    // Redirecionar para a mesma página após a criação do cardápio
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Excluir cardápio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cardapio'])) {
    $cardapio_id = $_POST['cardapio_id'];

    // Excluir relações antes de excluir o cardápio
    $conn->prepare("DELETE FROM cardapio_refeicao WHERE ID_Cardapio = ?")->execute([$cardapio_id]);
    $conn->prepare("DELETE FROM nutricionista_cria_cardapio WHERE ID_Cardapio = ?")->execute([$cardapio_id]);
    $conn->prepare("DELETE FROM cardapio WHERE ID_Cardapio = ?")->execute([$cardapio_id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Buscar refeições disponíveis
$refeicoes = $conn->query("SELECT * FROM refeicao")->fetchAll(PDO::FETCH_ASSOC);

// Buscar cardápios do nutricionista
$sql = "SELECT c.ID_Cardapio, c.Data, c.Descricao FROM cardapio c
        JOIN nutricionista_cria_cardapio ncc ON c.ID_Cardapio = ncc.ID_Cardapio
        WHERE ncc.CPF = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$cpf_nutricionista]);
$cardapios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutricionista - Gerenciamento de Cardápio</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 flex justify-between">
        <h1 class="text-white text-2xl font-bold">Gerenciamento de Cardápio</h1>
        <a href="../public/logout.php" class="text-white bg-red-500 px-4 py-2 rounded">Logout</a>
    </nav>

    <div class="container mx-auto p-8">
        <!-- Formulário para adicionar refeição -->
        <div class="bg-white p-6 rounded shadow-md mt-6">
            <h2 class="text-xl font-semibold">Adicionar Nova Refeição</h2>
            <form method="POST">
                <input type="text" name="proteina" placeholder="Proteína" required class="w-full p-2 border rounded mt-2">
                <input type="text" name="carboidrato" placeholder="Carboidrato" required class="w-full p-2 border rounded mt-2">
                <input type="text" name="salada" placeholder="Salada" required class="w-full p-2 border rounded mt-2">
                <input type="text" name="acompanhamento" placeholder="Acompanhamento" required class="w-full p-2 border rounded mt-2">
                <button type="submit" name="add_refeicao" class="w-full bg-blue-600 text-white p-2 rounded mt-4">Adicionar</button>
            </form>
        </div>

        <!-- Lista de refeições cadastradas -->
        <div class="bg-white p-6 rounded shadow-md mt-6">
            <h2 class="text-xl font-semibold">Refeições Cadastradas</h2>
            <ul>
                <?php foreach ($refeicoes as $refeicao) : ?>
                    <li class="border-b py-2 flex justify-between items-center">
                        <div>
                            <?= htmlspecialchars($refeicao['Proteina']) ?>, 
                            <?= htmlspecialchars($refeicao['Carboidrato']) ?>, 
                            <?= htmlspecialchars($refeicao['Salada']) ?>, 
                            <?= htmlspecialchars($refeicao['Acompanhamento']) ?>
                        </div>
                        <form method="POST" class="inline">
                            <input type="hidden" name="refeicao_id" value="<?= $refeicao['Codigo'] ?>">
                            <button type="submit" name="delete_refeicao" class="bg-red-600 text-white px-4 py-2 rounded">Excluir</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Formulário para criar cardápio -->
        <div class="bg-white p-6 rounded shadow-md mt-6">
            <h2 class="text-xl font-semibold">Criar Novo Cardápio</h2>
            <form method="POST">
                <input type="date" name="data" required class="w-full p-2 border rounded mt-2">
                <textarea name="descricao" placeholder="Descrição" required class="w-full p-2 border rounded mt-2"></textarea>
                <h3 class="mt-4">Selecione as Refeições:</h3>
                <?php foreach ($refeicoes as $refeicao) : ?>
                    <div>
                        <input type="checkbox" name="refeicoes[]" value="<?= $refeicao['Codigo'] ?>">
                        <?= htmlspecialchars($refeicao['Proteina']) ?>, <?= htmlspecialchars($refeicao['Carboidrato']) ?>,
                        <?= htmlspecialchars($refeicao['Salada']) ?>, <?= htmlspecialchars($refeicao['Acompanhamento']) ?>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="create_cardapio" class="w-full bg-green-600 text-white p-2 rounded mt-4">Criar Cardápio</button>
            </form>
        </div>

        <!-- Lista de cardápios -->
        <div class="bg-white p-6 rounded shadow-md mt-6">
            <h2 class="text-xl font-semibold">Cardápios Cadastrados</h2>
            <ul>
                <?php foreach ($cardapios as $cardapio) : ?>
                    <li class="border-b py-2 flex justify-between items-center">
                        <div>
                            <strong>Data:</strong> <?= htmlspecialchars($cardapio['Data']) ?> <br>
                            <strong>Descrição:</strong> <?= htmlspecialchars($cardapio['Descricao']) ?>
                        </div>
                        <form method="POST" class="inline">
                            <input type="hidden" name="cardapio_id" value="<?= $cardapio['ID_Cardapio'] ?>">
                            <button type="submit" name="delete_cardapio" class="bg-red-600 text-white px-4 py-2 rounded">Excluir</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
