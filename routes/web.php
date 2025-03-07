<?php
$routes = [
    'home' => '/sistema_alimentacao/views/dashboard.php',
    'login' => '/sistema_alimentacao/public/login.php',
    'recargas' => '/sistema_alimentacao/public/recargas.php',
    'transacoes' => '/sistema_alimentacao/public/transacoes.php',
    'logout' => '/sistema_alimentacao/public/logout.php',
    'dashboard' => '/sistema_alimentacao/views/dashboard.php'
];

function route($name) {
    global $routes;
    return $routes[$name] ?? '/public/404.php';
}
?>
