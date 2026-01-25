<?php
require_once 'clases/Usuario.php';
require_once 'layouts/header.php';
require_once 'conectar_db.php';

// Control de seguridad
if (!isset($_SESSION['usuario']) || (!$_SESSION['usuario']->esAdmin() && $_SESSION['usuario']->getRol() !== 'empleado')) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

// PROCESAR ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $id = $_POST['codigo'];
    $nuevo_stock = (int)$_POST['stock'];

    $stmt = $pdo->prepare("UPDATE articulos SET stock = ? WHERE codigo = ?");
    if ($stmt->execute([$nuevo_stock, $id])) {
        $mensaje = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check-circle me-2'></i>Stock actualizado correctamente.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    }
}

// OBTENER PRODUCTOS (con buscador opcional)
$busqueda = $_GET['buscar'] ?? '';
$sql = "SELECT codigo, nombre, stock, precio FROM articulos WHERE nombre LIKE ? ORDER BY nombre ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$busqueda%"]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0"><i class="bi bi-box-seam me-2"></i>Control de Inventario</h2>
        <form class="d-flex" method="GET">
            <input class="form-control me-2 rounded-pill" type="search" name="buscar" placeholder="Buscar producto..." value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-dark rounded-pill" type="submit">Buscar</button>
        </form>
    </div>

    <?= $mensaje ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Artículo</th>
                        <th>Precio</th>
                        <th style="width: 200px;">Stock Actual</th>
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td class="ps-4 text-muted">#<?= $prod['codigo'] ?></td>
                        <td><strong><?= htmlspecialchars($prod['nombre']) ?></strong></td>
                        <td><?= number_format($prod['precio'], 2) ?>€</td>
                        <td>
                            <form action="" method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="codigo" value="<?= $prod['codigo'] ?>">
                                <input type="number" name="stock" 
                                       class="form-control form-control-sm text-center <?= $prod['stock'] <= 5 ? 'border-danger text-danger fw-bold' : '' ?>" 
                                       value="<?= $prod['stock'] ?>" min="0">
                        </td>
                        <td class="text-end pe-4">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bi bi-arrow-repeat me-1"></i> Actualizar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 mb-5">
        <?php $url_panel = ($_SESSION['usuario']->esAdmin()) ? 'admin_panel.php' : 'empleado_panel.php'; ?>
        <a href="<?= $url_panel ?>" class="btn btn-light border-secondary-subtle text-secondary rounded-pill px-4 shadow-sm" style="background-color: #fff; border: 1px solid #ced4da;">
            <i class="bi bi-arrow-left me-2"></i> Volver al Panel
        </a>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>