<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../routes/web.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: " . route('login'));
    exit();
}

header("Location: " . route('home'));
exit();
?>
