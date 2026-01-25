<?php
require_once 'conectar_db.php';
require_once 'clases/Usuario.php';
require_once 'layouts/header.php';

// control de acceso o seguridad: Solo el admin puede entrar
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esAdmin()) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();

//Estadísticas Generales (Total ingresos y total pedidos)
//excluimos los pedidos cancelados (estado 3) para que no figuren en los ingresos
$sql_resumen = "SELECT 
                    COUNT(*) as total_pedidos, 
                    SUM(CASE WHEN estado != 3 THEN total ELSE 0 END) as ingresos_totales 
                FROM pedidos";
$resumen = $pdo->query($sql_resumen)->fetch(PDO::FETCH_ASSOC);

//pedidos pendientes de gestionar (estado 0)
$sql_pendientes = "SELECT COUNT(*) FROM pedidos WHERE estado = 0";
$pendientes = $pdo->query($sql_pendientes)->fetchColumn();

//Top 5 Productos más vendidos (hacemos un JOIN con lineapedido)
$sql_top = "SELECT nombre, SUM(cantidad) as unidades 
            FROM lineapedido 
            JOIN articulos ON lineapedido.codArticulo = articulos.codigo 
            GROUP BY nombre 
            ORDER BY unidades DESC 
            LIMIT 5";
$top_productos = $pdo->query($sql_top)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="bi bi-bar-chart-line me-2"></i>Reportes de Venta</h2>
            <p class="text-muted">Resumen de actividad de Monkey's Elx</p>
        </div>
        <a href="admin_panel.php" class="btn btn-light border-secondary-subtle text-secondary rounded-pill px-4 shadow-sm" style="background-color: #fff; border: 1px solid #ced4da;">
            <i class="bi bi-arrow-left me-2"></i> Volver al Panel
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Ingresos Totales</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($resumen['ingresos_totales'] ?? 0, 2, ',', '.') ?> €</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0d6efd !important;">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Pedidos Realizados</h6>
                    <h2 class="fw-bold mb-0"><?= $resumen['total_pedidos'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small fw-bold">Por Gestionar</h6>
                    <h2 class="fw-bold mb-0"><?= $pendientes ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Top 5 Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($top_productos as $index => $prod): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="badge bg-secondary rounded-pill me-2"><?= $index + 1 ?></span>
                                    <?= $prod['nombre'] ?>
                                </div>
                                <span class="fw-bold"><?= $prod['unidades'] ?> uds.</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-dark text-white p-4 h-100">
                <h5 class="fw-bold">Acciones de Inventario</h5>
                <p class="opacity-75">¿Necesitas añadir nuevos productos o revisar el stock actual?</p>
                <div class="mt-auto">
                    <a href="gestionar_articulos.php" class="btn btn-outline-light rounded-pill w-100 mb-2">Gestionar Artículos</a>
                    <a href="gestionar_pedidos.php" class="btn btn-warning rounded-pill w-100 fw-bold text-dark">Ver todos los pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>