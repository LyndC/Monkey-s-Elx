<?php
require_once 'clases/Articulo.php';
require_once 'conectar_db.php'; 
session_start();

$codigo = $_GET['codigo'] ?? '';

if ($codigo) {
    $pdo = conectar(); 
    
    // preparamos la consulta sql
    $stmt = $pdo->prepare("SELECT * FROM articulos WHERE codigo = ? AND activo = 1");
    $stmt->execute([$codigo]);
    
    //convertimos el resultado en un objeto de nuestra clase Articulo
    $articulo = $stmt->fetchObject('Articulo');

    if ($articulo && $articulo->hayStock()) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        //controlamos que no añada más del stock que tenemos en la BD
        if (isset($_SESSION['carrito'][$codigo])) {
            if ($_SESSION['carrito'][$codigo] < $articulo->getStock()) {
                $_SESSION['carrito'][$codigo]++;
            }
        } else {
            $_SESSION['carrito'][$codigo] = 1;
        }
        
        header("Location: carrito.php");
        exit;
        
    } else {
        header("Location: index.php?error=producto_no_valido");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}