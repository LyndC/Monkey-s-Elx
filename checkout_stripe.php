<?php
//cargamos el autoloader de Composer
require_once 'vendor/autoload.php';
require_once 'conectar_db.php'; 
session_start();

// clave secreta (de stripe en modo prueba)
\Stripe\Stripe::setApiKey('TukeyAqui');

$items_carrito = [];
$pdo = conectar();

// convertimos los articulos a productos de Stripe
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $codigo => $cantidad) {
        // añadimos 'descuento' a la consulta SQL para que se muestre en stripe
        $stmt = $pdo->prepare("SELECT nombre, precio, descuento FROM articulos WHERE codigo = ?");
        $stmt->execute([$codigo]);
        $art = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($art) {
            //calculamos el descuento
            $precio_base = $art['precio'];
            $porcentaje_desc = $art['descuento'] ?? 0; //si no hay nada será 0
            
            //formula: Precio - (Precio * % / 100)
            $precio_final = $precio_base - ($precio_base * ($porcentaje_desc / 100));

            //convertimos a céntimos (Redondeando para evitar errores de decimales)
            $monto_centimos = round($precio_final * 100);

            $items_carrito[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $art['nombre'] . ($porcentaje_desc > 0 ? " (-$porcentaje_desc%)" : "")
                    ],
                    'unit_amount' => $monto_centimos, 
                ],
                'quantity' => $cantidad,
            ];
        }
    }
}

//creamos la sesión de pago
try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $items_carrito,
        'mode' => 'payment',
        // A dónde va el usuario tras pagar o cancelar
        'success_url' => 'http://localhost/confirmar_pedido.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/carrito.php',
    ]);

    // Redirigimos automáticamente a la pasarela de Stripe
    header("Location: " . $session->url);
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}