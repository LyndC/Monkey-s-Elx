<?php
//debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'clases/Usuario.php';
require_once 'vendor/autoload.php';
require_once 'conectar_db.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = conectar();

// Configuración de Stripe
\Stripe\Stripe::setApiKey('TuApyKEYAqui');
\Stripe\Stripe::setVerifySslCerts(false); 
//pago con tarjeta o en tienda
$session_id = $_GET['session_id'] ?? null;
$metodo_pago = ($session_id) ? 'tarjeta' : 'tienda';
$estado = ($session_id) ? 'pagado' : 'pendiente';

if (isset($_SESSION['usuario'])) {
    $usuarioObjeto = $_SESSION['usuario'];
    $codUsuario = $usuarioObjeto->getDni(); 
} else {
    header("Location: login.php");
    exit;
}

$total = 0;
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $codigo => $cantidad) {
        $stmt = $pdo->prepare("SELECT precio, descuento FROM articulos WHERE codigo = ?");
        $stmt->execute([$codigo]);
        $art = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($art) {
            $precio_con_desc = $art['precio'] - ($art['precio'] * ($art['descuento'] / 100));
            $total += ($precio_con_desc * $cantidad);
        }
    }
} else {
    die("El carrito está vacío.");
}

$codigo_recogida = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

try {
    $pdo->beginTransaction();

    //Insertar Pedido
    $sqlPedido = "INSERT INTO pedidos (fecha, total, metodo_pago, estado, codUsuario, codigo_recogida, activo)  
                  VALUES (NOW(), ?, ?, ?, ?, ?, 1)";
    $stmtPedido = $pdo->prepare($sqlPedido);
    $stmtPedido->execute([$total, $metodo_pago, $estado, $codUsuario, $codigo_recogida]);
    
    $idNuevoPedido = $pdo->lastInsertId();

    //insertar Líneas de Pedido
    $numLinea = 1;
    foreach ($_SESSION['carrito'] as $codigoArticulo => $cantidad) {
        $stmtArt = $pdo->prepare("SELECT precio, descuento FROM articulos WHERE codigo = ?");
        $stmtArt->execute([$codigoArticulo]);
        $artInfo = $stmtArt->fetch(PDO::FETCH_ASSOC);
        
        if ($artInfo) {
            $porcentaje = isset($artInfo['descuento']) ? (float)$artInfo['descuento'] : 0;
            $precio_original = (float)$artInfo['precio'];
            //calculamos el valor del descuento en euros
            $descuento_euros = $precio_original * ($porcentaje / 100);
            
            $sqlLinea = "INSERT INTO lineapedido (numPedido, numLinea, codArticulo, cantidad, precio_unitario, descuento_aplicado)  
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmtL = $pdo->prepare($sqlLinea);
            $stmtL->execute([$idNuevoPedido, $numLinea, $codigoArticulo, $cantidad, $artInfo['precio'], $descuento_euros]);
            $numLinea++;
        }
    }       

    $pdo->commit();
    unset($_SESSION['carrito']);

    
    require_once 'layouts/header.php'; // Cargamos cabecera para mantener el menú
    ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>
                        
                        <h1 class="display-5 fw-bold mb-3">¡Pedido Confirmado!</h1>
                        <p class="lead text-muted mb-4">Gracias por tu compra. Hemos procesado tu pedido correctamente.</p>
                        
                        <div class="bg-light p-4 rounded-3 mb-4 border">
                            <h5 class="text-uppercase tracking-wider small text-muted mb-2">Código de recogida único</h5>
                            <span class="display-6 fw-bold text-success" style="letter-spacing: 5px;">
                                <?php echo $codigo_recogida; ?>
                            </span>
                            <p class="small text-muted mt-2">Muestra este código en caja al momento de retirar tus productos.</p>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="index.php" class="btn btn-primary btn-lg px-4 gap-3">Volver a la tienda</a>
                            <button onclick="window.print()" class="btn btn-outline-secondary btn-lg px-4">Imprimir recibo</button>
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-muted small">Se ha enviado un detalle de la transacción a tu cuenta.</p>
            </div>
        </div>
    </div>

    <?php 
    require_once 'layouts/footer.php'; 

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    //en caso de error, también le damos formato
    require_once 'layouts/header.php';
    echo "<div class='container mt-5'><div class='alert alert-danger'>
            <h4>Error en el proceso</h4>
            <p>Lo sentimos, hubo un problema: " . $e->getMessage() . "</p>
            <a href='carrito.php' class='btn btn-danger'>Reintentar</a>
          </div></div>";
    require_once 'layouts/footer.php';
}
?>