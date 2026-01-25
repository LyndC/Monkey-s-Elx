<?php
require_once 'layouts/header.php';
require_once 'conectar_db.php';

// Verificamos que sea administrador
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esAdmin()) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

// baja lógica (Activar/Desactivar)
if (isset($_GET['toggle_status']) && isset($_GET['dni'])) {
    $nuevoEstado = $_GET['toggle_status'] == '1' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = ? WHERE dni = ?");
    $stmt->execute([$nuevoEstado, $_GET['dni']]);
    $mensaje = "<div class='alert alert-info py-2'>Estado de usuario actualizado.</div>";
}

//lógica para la busqueda
$busqueda = $_GET['buscar'] ?? '';
$filtroRol = $_GET['filtro_rol'] ?? '';

$sql = "SELECT * FROM usuarios WHERE (nombre LIKE ? OR dni LIKE ? OR email LIKE ?)";
$params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];

if ($filtroRol != '') {
    $sql .= " AND rol = ?";
    $params[] = $filtroRol;
}

$sql .= " ORDER BY apellidos ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h2>
            <p class="text-muted">Administra empleados, clientes y sus permisos.</p>
        </div>
        <div class="d-flex gap-2">
        <a href="admin_panel.php" class="btn btn-outline-dark rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> VOLVER AL PANEL
        </a>
        <a href="registro_interno.php" class="btn btn-dark rounded-pill px-4">
            <i class="bi bi-person-plus-fill me-2"></i> NUEVO USUARIO
        </a>
    </div>
</div>

    <?= $mensaje ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre, DNI o email..." value="<?= htmlspecialchars($busqueda) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="filtro_rol" class="form-select">
                        <option value="">Todos los roles</option>
                        <option value="cliente" <?= $filtroRol == 'cliente' ? 'selected' : '' ?>>Clientes</option>
                        <option value="empleado" <?= $filtroRol == 'empleado' ? 'selected' : '' ?>>Empleados</option>
                        <option value="admin" <?= $filtroRol == 'admin' ? 'selected' : '' ?>>Administradores</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-dark w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>DNI</th>
                        <th>Contacto</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr class="<?= $u['activo'] == 0 ? 'table-light opacity-75' : '' ?>">
                        <td class="ps-4">
                            <div class="fw-bold"><?= htmlspecialchars($u['nombre'] . " " . $u['apellidos']) ?></div>
                            <small class="text-muted">Alta: <?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></small>
                        </td>
                        <td><code class="text-dark"><?= htmlspecialchars($u['dni']) ?></code></td>
                        <td>
                            <div class="small"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($u['email']) ?></div>
                            <div class="small"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($u['telefono']) ?></div>
                        </td>
                        <td>
                            <?php 
                            $badgeClass = 'bg-info';
                            if($u['rol'] == 'admin') $badgeClass = 'bg-danger';
                            if($u['rol'] == 'empleado') $badgeClass = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?= $badgeClass ?> text-uppercase" style="font-size: 0.7rem;">
                                <?= $u['rol'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if($u['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center pe-4">
    <div class="btn-group shadow-sm">
        <?php if($u['dni'] === $_SESSION['usuario']->getDni()): ?>
            <span class="badge bg-light text-dark border py-2 px-3">
                <i class="bi bi-shield-lock-fill me-1"></i> Tu Cuenta
            </span>
        <?php else: ?>
            <a href="editar_usuario.php?dni=<?= $u['dni'] ?>" class="btn btn-sm btn-white border" title="Editar">
                <i class="bi bi-pencil-square text-primary"></i>
            </a>
            
            <a href="gestionar_usuarios.php?dni=<?= $u['dni'] ?>&toggle_status=<?= $u['activo'] == 1 ? '0' : '1' ?>" 
               class="btn btn-sm btn-white border" 
               title="<?= $u['activo'] == 1 ? 'Dar de baja' : 'Activar' ?>"
               onclick="return confirm('¿Cambiar estado de este usuario?')">
                <i class="bi <?= $u['activo'] == 1 ? 'bi-person-x-fill text-danger' : 'bi-person-check-fill text-success' ?>"></i>
            </a>
        <?php endif; ?>
    </div>
</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>