<?php
ob_start();
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php';

if (session_status() === PHP_SESSION_NONE) session_start();

//control de seguridad: empleados o admin
if (!isset($_SESSION['usuario']) || (!$_SESSION['usuario']->esAdmin() && $_SESSION['usuario']->getRol() !== 'empleado')) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

//en caso de devolucion la procesamos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_devolucion'])) {
    $idPedido = $_POST['id_pedido'];
    
    try {
        $pdo->beginTransaction();

        //obtenemos los productos del pedido para devolverlos al stock
        $stmtItems = $pdo->prepare("SELECT codArticulo, cantidad FROM lineapedido WHERE numPedido = ?");
        $stmtItems->execute([$idPedido]);
        $items = $stmtItems->fetchAll();

        foreach ($items as $item) {
            $stmtStock = $pdo->prepare("UPDATE articulos SET stock = stock + ? WHERE codigo = ?");
            $stmtStock->execute([$item['cantidad'], $item['codArticulo']]);
        }

        // cambiar estado a (3) cancelado
        $stmtUpdate = $pdo->prepare("UPDATE pedidos SET estado = 3 WHERE idPedido = ?");
        $stmtUpdate->execute([$idPedido]);

        $pdo->commit();
        $mensaje = "<div class='alert alert-warning'>🔄 Devolución procesada: Stock restaurado y pedido #$idPedido cancelado.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje = "<div class='alert alert-danger'>❌ Error en la devolución: " . $e->getMessage() . "</div>";
    }
}



//actualizar Estado 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado'])) {
    $idPedido = $_POST['id_pedido'];
    $nuevoEstado = $_POST['nuevo_estado'];
    
    $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE idPedido = ?");
    if ($stmt->execute([$nuevoEstado, $idPedido])) {
        $mensaje = "<div class='alert alert-success'>✅ Pedido #$idPedido actualizado.</div>";
    }
}

//consulta sql usando left join 
$sql = "SELECT p.*, u.nombre as nombre_cliente, u.email 
        FROM pedidos p 
        LEFT JOIN usuarios u ON p.codUsuario = u.dni 
        ORDER BY p.fecha DESC";

$stmt = $pdo->query($sql);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

//función para traducir los numeros del estado del pedido a texto
function getEstadoInfo($estado) {
    return match((int)$estado) {
        0 => ['txt' => 'PENDIENTE', 'class' => 'bg-warning text-dark'],
        1 => ['txt' => 'PAGADO', 'class' => 'bg-info text-dark'],
        2 => ['txt' => 'COMPLETADO', 'class' => 'bg-success'],
        3 => ['txt' => 'CANCELADO', 'class' => 'bg-danger'],
        default => ['txt' => 'DESCONOCIDO', 'class' => 'bg-secondary'],
    };
}
?>

<div class="container mt-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-shop me-2"></i>Gestión de Pedidos</h2>

    <?= $mensaje ?>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info text-center py-4">No hay pedidos registrados todavía.</div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Cliente</th>
                            <th>Pago</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                        <?php $info = getEstadoInfo($p['estado']); ?>
                        <tr>
                            <td class="ps-4">#<?= $p['idPedido'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($p['nombre_cliente'] ?? 'Invitado') ?></strong><br>
                                <small class="text-muted"><?= $p['codUsuario'] ?></small>
                            </td>
                            <td><span class="badge border text-dark"><?= strtoupper($p['metodo_pago']) ?></span></td>
                            <td class="fw-bold"><?= number_format($p['total'], 2) ?>€</td>
                            <td><span class="badge <?= $info['class'] ?>"><?= $info['txt'] ?></span></td>
                            <td class="text-end pe-4">
                                <div class="d-flex flex-column gap-2 align-items-end">
                                    <form action="" method="POST" class="d-inline-flex gap-2">
                                        <input type="hidden" name="id_pedido" value="<?= $p['idPedido'] ?>">
                                        <select name="nuevo_estado" class="form-select form-select-sm" style="width: auto;">
                                            <option value="0" <?= $p['estado']==0?'selected':'' ?>>Pendiente</option>
                                            <option value="1" <?= $p['estado']==1?'selected':'' ?>>Pagado</option>
                                            <option value="2" <?= $p['estado']==2?'selected':'' ?>>Completado</option>
                                            <option value="3" <?= $p['estado']==3?'selected':'' ?>>Cancelado</option>
                                        </select>
                                        <button type="submit" name="actualizar_estado" class="btn btn-sm btn-dark">OK</button>
                                    </form>

                                    <?php if ($p['estado'] != 3): ?>
                                    <form action="" method="POST" onsubmit="return confirm('¿Confirmar devolución? Se sumará el stock de nuevo.');">
                                        <input type="hidden" name="id_pedido" value="<?= $p['idPedido'] ?>">
                                        <button type="submit" name="procesar_devolucion" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Devolución Total
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 mb-5">
            <?php $url_retorno = ($_SESSION['usuario']->esAdmin()) ? 'admin_panel.php' : 'empleado_panel.php'; ?>
            <a href="<?= $url_retorno ?>" 
               class="btn btn-light border-secondary-subtle text-secondary rounded-pill px-4 shadow-sm" 
               style="background-color: #fff; border: 1px solid #ced4da;">
                <i class="bi bi-arrow-left me-2"></i> Volver al Panel
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'layouts/footer.php'; ?>