<?php
ob_start();
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Control de seguridad: Solo clientes logueados
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$pdo = conectar();
$idPedido = $_GET['id'] ?? null;
$dni_cliente = $_SESSION['usuario']->getDni(); 

if (!$idPedido) {
    header("Location: mis_pedidos.php");
    exit;
}

//consulta sql a lineapedido usando join con articulos y pedidos
$sql = "SELECT lp.cantidad, lp.precio_unitario,a.descuento, a.nombre, a.imagen 
        FROM lineapedido lp
        JOIN articulos a ON lp.codArticulo = a.codigo
        JOIN pedidos p ON lp.numPedido = p.idPedido
        WHERE lp.numPedido = ? AND p.codUsuario = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idPedido, $dni_cliente]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_real_pedido = 0;
foreach ($detalles as $item) {
    $p_orig = $item['precio_unitario'];
    $desc = $item['descuento'] ?? 0;
    $precio_desc = $p_orig - ($p_orig * ($desc / 100));
    $total_real_pedido += ($item['cantidad'] * $precio_desc);
}

//consulta adicional para datos generales del pedido (fecha, total, estado)
$sqlPedido = "SELECT * FROM pedidos WHERE idPedido = ? AND codUsuario = ?";
$stmtP = $pdo->prepare($sqlPedido);
$stmtP->execute([$idPedido, $dni_cliente]);
$pedidoInfo = $stmtP->fetch(PDO::FETCH_ASSOC);

//si no existe el pedido o no pertenece al cliente, vuelve e mis_pedidos
if (!$pedidoInfo) {
    header("Location: mis_pedidos.php");
    exit;
}
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="mis_pedidos.php">Mis Pedidos</a></li>
        <li class="breadcrumb-item active">Detalle #<?= $idPedido ?></li>
      </ol>
    </nav>

    <?php 
    //calculamos el total real antes de empezar la tabla para que el badge de arriba sea correcto
    $total_calculado = 0;
    foreach ($detalles as $item) {
        $p_base = $item['precio_unitario'];
        $desc = $item['descuento'] ?? 0;
        $total_calculado += ($item['cantidad'] * ($p_base - ($p_base * ($desc / 100))));
    }
    ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0">Pedido REC-<?= str_pad($idPedido, 4, "0", STR_PAD_LEFT) ?></h3>
                <span class="badge bg-primary px-3 py-2 fs-6">Pagado: <?= number_format($total_calculado, 2) ?>€</span>
            </div>
            <p class="text-muted small">Realizado el <?= date('d/m/Y H:i', strtotime($pedidoInfo['fecha'])) ?></p>
            <hr>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary">
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $item): ?>
                            <?php 
                                $precio_orig = $item['precio_unitario'];
                                $porcentaje = $item['descuento'] ?? 0;
                                $descuento_en_euros = $precio_orig * ($porcentaje / 100);
                                $precio_final = $precio_orig - $descuento_en_euros;
                                $subtotal_linea = $item['cantidad'] * $precio_final;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" class="rounded me-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($item['nombre']) ?></div>
                                            <?php if ($porcentaje > 0): ?>
                                                <small class="text-success fw-bold">
                                                    <i class="bi bi-tag-fill"></i> Oferta -<?= $porcentaje ?>%
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><?= $item['cantidad'] ?></td>
                                <td class="text-end">
                                    <?php if ($porcentaje > 0): ?>
                                        <del class="text-muted small"><?= number_format($precio_orig, 2) ?>€</del><br>
                                    <?php endif; ?>
                                    <span class="fw-bold"><?= number_format($precio_final, 2) ?>€</span>
                                </td>
                                <td class="text-end fw-bold">
                                    <?= number_format($subtotal_linea, 2) ?>€
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total a pagar:</td>
                            <td class="text-end fw-bold text-primary fs-5"><?= number_format($total_calculado, 2) ?>€</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <a href="mis_pedidos.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i> Volver a mis pedidos
        </a>
    </div>
</div>
<?php  require_once 'layouts/footer.php' ?>