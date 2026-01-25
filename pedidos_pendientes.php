<?php
require_once 'clases/Usuario.php';
require_once 'layouts/header.php'; // Aquí ya se inicia la sesión
require_once 'conectar_db.php';

//control de seguridad: empleados o admin
if (!isset($_SESSION['usuario']) || (!$_SESSION['usuario']->esAdmin() && $_SESSION['usuario']->getRol() !== 'empleado')) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();

// Consulta para obtener pedidos pendientes con datos del usuario
$sql = "SELECT p.*, u.nombre, u.apellidos as cliente 
        FROM pedidos p 
        JOIN usuarios u ON p.codUsuario = u.dni
        WHERE p.estado = 0 
        ORDER BY p.fecha DESC";
$pedidos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-box-seam me-2"></i>Pedidos Pendientes</h2>

    <?php if (count($pedidos) > 0): ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle bg-white mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                        <tr>
                            <td class="fw-bold">#<?= $p['idPedido'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['fecha'])) ?></td>
                            <td><?= htmlspecialchars($p['cliente']) ?></td>
                            <td class="fw-bold"><?= number_format($p['total'], 2, ',', '.') ?>€</td>
                            <td class="text-center">
                                <a href="gestionar_pedidos.php?id=<?= $p['idPedido'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-emoji-sunglasses display-1"></i>
            <p class="mt-3 lead">¡Todo al día! No hay pedidos pendientes de envío.</p>
        </div>
    <?php endif; ?>
</div>
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-start">
        <?php 
            // Verificamos el rol para decidir a qué panel enviarlo
            $url_panel = ($_SESSION['usuario']->esAdmin()) ? 'admin_panel.php' : 'empleado_panel.php';
        ?>
        <a href="<?= $url_panel ?>" 
           class="btn btn-light border-secondary-subtle text-secondary rounded-pill px-4 shadow-sm" 
           style="background-color: #fff; border: 1px solid #ced4da;">
            <i class="bi bi-arrow-left me-2"></i> Volver al Panel
        </a>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>