<?php
ob_start();
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php';

if (session_status() === PHP_SESSION_NONE) session_start();

//control de seguridad: Solo clientes logueados
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$pdo = conectar();
$dni_cliente = $_SESSION['usuario']->getDni(); 

//traemos los pedidos de este cliente específico
$sql = "SELECT idPedido, fecha, total, estado, metodo_pago 
        FROM pedidos 
        WHERE codUsuario = ? 
        ORDER BY fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$dni_cliente]);
$mis_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getEstadoCliente($estado) {
    return match((int)$estado) {
        0 => ['txt' => 'En preparación', 'class' => 'bg-warning text-dark'],
        1 => ['txt' => 'Listo para recoger', 'class' => 'bg-info text-dark'],
        2 => ['txt' => 'Entregado', 'class' => 'bg-success'],
        3 => ['txt' => 'Cancelado', 'class' => 'bg-danger'],
        default => ['txt' => 'Pendiente', 'class' => 'bg-secondary'],
    };
}
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold"><i class="bi bi-bag-check me-2"></i>Mis Pedidos</h2>
            <p class="text-muted">Consulta el estado de tus compras y tus códigos de recogida.</p>
        </div>
    </div>

    <?php if (empty($mis_pedidos)): ?>
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <h4 class="mt-3">Aún no has realizado pedidos</h4>
                <a href="productos.php" class="btn btn-primary rounded-pill mt-3 px-4">Ir a la tienda</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($mis_pedidos as $p): 
                $info = getEstadoCliente($p['estado']);
                // leemos el codigo de recogida
               $codigo_recogida = !empty($p['codigo_recogida']) ? $p['codigo_recogida'] : "REC-" . str_pad($p['idPedido'], 4, "0", STR_PAD_LEFT);
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between">
                            <span class="text-muted small"><?= date('d/m/Y', strtotime($p['fecha'])) ?></span>
                            <span class="badge <?= $info['class'] ?>"><?= $info['txt'] ?></span>
                        </div>
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">Código de Recogida:</h6>
                            <h3 class="fw-bold text-primary mb-3"><?= $codigo_recogida ?></h3>
                            
                            <div class="d-flex justify-content-between border-top pt-3">
                                <span>Total:</span>
                                <span class="fw-bold"><?= number_format($p['total'], 2) ?>€</span>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 pb-3">
                            <a href="detalle_pedido.php?id=<?= $p['idPedido'] ?>" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                                <i class="bi bi-search me-1"></i> Ver detalles / Productos
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-5 mb-5">
        <a href="index.php" class="btn btn-light border-secondary-subtle text-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> Volver a la Tienda
        </a>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>