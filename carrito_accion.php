<?php
//archivo controlador para que los botones +, -  y eliminar funcionen
session_start();
require_once 'conectar_db.php';

$action = $_GET['action'] ?? '';
$cod = $_GET['cod'] ?? '';

if ($cod && isset($_SESSION['carrito'][$cod])) {
    $pdo = conectar();
    //consultamos el stock máximo
    $stmt = $pdo->prepare("SELECT stock FROM articulos WHERE codigo = ?");
    $stmt->execute([$cod]);
    $stock_max = $stmt->fetchColumn();

    if ($action === 'incrementar' && $_SESSION['carrito'][$cod] < $stock_max) {
        $_SESSION['carrito'][$cod]++;
    } elseif ($action === 'decrementar') {
        $_SESSION['carrito'][$cod]--;
        if ($_SESSION['carrito'][$cod] < 1) unset($_SESSION['carrito'][$cod]);
    } elseif ($action === 'eliminar') {
        unset($_SESSION['carrito'][$cod]);
    }
}

header("Location: carrito.php");
exit;