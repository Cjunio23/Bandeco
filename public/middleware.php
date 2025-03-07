<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar permissões
function verificarPermissao($papel) {
    if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== $papel) {
        header("Location: ../views/login.php");
        exit();
    }
}
?>