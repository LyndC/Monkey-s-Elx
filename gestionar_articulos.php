<?php
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// control de eguridad: Solo Admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

//lógica para elliminar (Borrado lógico)
if (isset($_GET['eliminar'])) {
    $codigo = $_GET['eliminar'];
    $stmt = $pdo->prepare("UPDATE articulos SET activo = 0 WHERE codigo = ?");
    if ($stmt->execute([$codigo])) {
        $mensaje = "<div class='alert alert-success'>Artículo desactivado correctamente.</div>";
    }
}

// hacemos JOIN para obtener el nombre de la categoría
$sql = "SELECT a.*, c.nombre as nombre_categoria 
        FROM articulos a 
        LEFT JOIN categorias c ON a.categoria = c.codigo 
        WHERE a.activo = 1 
        ORDER BY a.codigo DESC";
$stmt = $pdo->query($sql);
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="bi bi-box-seam me-2"></i>Gestión de Artículos</h2>
            <p class="text-muted">Inventario actual y categorías</p>
        </div>
        <a href="nuevo_articulo.php" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i>Añadir Nuevo
        </a>
    </div>

    <?= $mensaje ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articulos as $art): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?= $art['codigo'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= $art['imagen'] ?>" alt="" class="rounded me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($art['nombre']) ?></div>
                                        <small class="text-muted"><?= substr(htmlspecialchars($art['descripcion']), 0, 40) ?>...</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($art['nombre_categoria'] ?? 'Sin categoría') ?></span>
                            </td>
                            <td class="fw-bold"><?= number_format($art['precio'], 2) ?>€</td>
                            <td>
                                <span class="badge <?= $art['stock'] < 5 ? 'bg-danger' : 'bg-light text-dark border' ?>">
                                    <?= $art['stock'] ?> uds
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="editar_articulo.php?id=<?= $art['codigo'] ?>" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="gestionar_articulos.php?eliminar=<?= $art['codigo'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('¿Seguro que quieres desactivar este producto?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="admin_panel.php" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i>Volver al Panel
        </a>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>