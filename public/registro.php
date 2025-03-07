<?php
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); // Remove caracteres não numéricos
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // Verifica se o CPF já está cadastrado
    $stmt = $pdo->prepare("SELECT cpf FROM usuario WHERE cpf = :cpf");
    $stmt->bindParam(':cpf', $cpf);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $erro = "CPF já cadastrado!";
    } else {
        // Insere o usuário no banco de dados
        $stmt = $pdo->prepare("INSERT INTO usuario (cpf, nome, email, senha) VALUES (:cpf, :nome, :email, :senha)");
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $erro = "Erro ao cadastrar usuário!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Alimentação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-200">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-sm w-full">
            <h1 class="text-center text-3xl font-bold text-blue-600">Bandeco</h1>
            <h2 class="text-center text-xl mt-2">Registro</h2>
            
            <?php if (isset($erro)): ?>
                <div class="text-red-500 text-center mt-4"><?= htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mt-4">
                    <label class="block text-sm">CPF</label>
                    <input type="text" name="cpf" required class="w-full p-2 border rounded" placeholder="Digite seu CPF">
                </div>
                <div class="mt-4">
                    <label class="block text-sm">Nome</label>
                    <input type="text" name="nome" required class="w-full p-2 border rounded">
                </div>
                <div class="mt-4">
                    <label class="block text-sm">E-mail</label>
                    <input type="email" name="email" required class="w-full p-2 border rounded">
                </div>
                <div class="mt-4">
                    <label class="block text-sm">Senha</label>
                    <input type="password" name="senha" required class="w-full p-2 border rounded">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mt-4">Cadastrar</button>
            </form>

            <div class="mt-4 text-center">
                <a href="login.php" class="text-blue-500">Já tem uma conta? Faça login</a>
            </div>
        </div>
    </div>
</body>
</html>

