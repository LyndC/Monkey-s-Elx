<?php
session_start();
//
require_once 'conectar_db.php'; 
$pdo = conectar();
$mensaje = "";

// crear nueva categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    $nombre_categoria = trim($_POST['nombre_categoria']);

    if (empty($nombre_categoria)) {
        $mensaje = "<div class='alert alert-danger py-2'>Error: El nombre de la categoría no puede estar vacío.</div>";
    } else {
        try {
            // Buscamos si ya existe por el campo 'nombre'
            $check = $pdo->prepare("SELECT codigo FROM categorias WHERE nombre = ?");
            $check->execute([$nombre_categoria]);
            
            if ($check->fetch()) {
                $mensaje = "<div class='alert alert-danger py-2'>Error: La categoría <b>$nombre_categoria</b> ya existe.</div>";
            } else {
                
                $ins = $pdo->prepare("INSERT INTO categorias (nombre, descripcion, imagen, activo) VALUES (?, NULL, NULL, 1)");
                $ins->execute([$nombre_categoria]);
                
                $mensaje = "<div class='alert alert-success py-2'>Categoría <b>$nombre_categoria</b> creada correctamente.</div>";
            }
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger py-2'>Error en la base de datos: " . $e->getMessage() . "</div>";
        }
    }
}

//obtener todas las categorías para mostrar en la tabla
try {
    $consulta = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $consulta->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar las categorías: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-tags-fill me-2 text-primary"></i>Gestión de Categorías</h2>
        <a href="admin_panel.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver al Panel</a>
    </div>

    <?php echo $mensaje; ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Nueva Categoría</h5>
                    <form action="gestionar_categorias.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre de la Categoría</label>
                            <input type="text" name="nombre_categoria" class="form-control" placeholder="Ej: Ropa, Relojes..." required>
                        </div>
                        <button type="submit" name="crear_categoria" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-1"></i> Crear Categoría
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4" style="width: 20%;">Código</th>
                                    <th>Nombre de la Categoría</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($categorias) > 0): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <tr>
                                            <td class="ps-4 text-muted">#<?php echo $cat['codigo']; ?></td>
                                            <td class="fw-bold text-secondary"><?php echo htmlspecialchars($cat['nombre']); ?></td>
                                            <td class="text-center">
                                                <?php if ($cat['activo'] == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No hay categorías registradas en el sistema.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>