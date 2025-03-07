<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscar usuário pelo e-mail e verificar senha
    $stmt = $pdo->prepare("SELECT cpf, senha, role FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION["user_cpf"] = $user["cpf"];
        $_SESSION["user_role"] = $user["role"];

        // Redirecionamento conforme o papel do usuário
        if ($user["role"] === 'admin') {
            header("Location: ../views/adm_dashboard.php");
        } elseif ($user["role"] === 'nutricionista') {
            header("Location: ../views/nutri_dashboard.php");
        } else {
            header("Location: ../views/dashboard.php");
        }
        exit();
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Alimentação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-200">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-sm w-full">
            <h1 class="text-center text-3xl font-bold text-blue-600">Bandeco</h1>
            <h2 class="text-center text-xl mt-2">Login</h2>

            <?php if (isset($erro)): ?>
                <div class="text-red-500 text-center mt-4"><?= htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mt-4">
                    <label class="block text-sm">E-mail</label>
                    <input type="email" name="email" required class="w-full p-2 border rounded">
                </div>
                <div class="mt-4">
                    <label class="block text-sm">Senha</label>
                    <input type="password" name="senha" required class="w-full p-2 border rounded">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mt-4">Entrar</button>
            </form>

            <div class="mt-4 text-center">
                <a href="registro.php" class="text-blue-500">Criar conta</a>
            </div>
        </div>
    </div>
</body>
</html>
