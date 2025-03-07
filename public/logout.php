<?php
require_once __DIR__ . '/../routes/web.php'; // Incluindo o arquivo web.php
require_once __DIR__ . '/../config/database.php';

session_start();
session_destroy();
header("Location: " . route('login'));  // Redireciona para a página de login após logout
exit();
