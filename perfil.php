<?php
require_once 'layouts/header.php'; 
require_once 'conectar_db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$dni = $usuario->getDni();
$mensaje = "";

//lógica de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $direccion = trim($_POST['direccion']);
    $localidad = trim($_POST['localidad']);
    $provincia = trim($_POST['provincia']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);

    try {
        $pdo = conectar();
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, direccion=?, localidad=?, provincia=?, telefono=?, email=? WHERE dni=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellidos, $direccion, $localidad, $provincia, $telefono, $email, $dni]);

        //actualizamos el objeto en la sesión para que los cambios se vean reflejados de inmediato
        $usuario->setNombre($nombre);
        $usuario->setApellidos($apellidos);
        $usuario->setDireccion($direccion);
        $usuario->setLocalidad($localidad);
        $usuario->setProvincia($provincia);
        $usuario->setTelefono($telefono);
        $usuario->setEmail($email);
        
        $mensaje = "<div class='alert alert-success'>Perfil actualizado correctamente.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $e->getMessage() . "</div>";
    }
}

//lógica para obtener el historial de pedidos(Si es cliente o empleado comprando)
$pedidos = [];
try {
    $pdo = conectar();
    $stmtPedidos = $pdo->prepare("SELECT * FROM pedidos WHERE dni_usuario = ? ORDER BY fecha DESC");
    $stmtPedidos->execute([$dni]);
    $pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // captura de errores
}
?>

<div class="container mt-5 mb-5">
    <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <?php 
            $url_panel = "cliente_panel.php"; // Por defecto
            if ($usuario->getRol() === 'admin') $url_panel = "admin_panel.php";
            if ($usuario->getRol() === 'empleado') $url_panel = "empleado_panel.php";
        ?>
        <a href="<?= $url_panel ?>" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Volver al Panel
        </a>
    </div>
</div>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-person-gear me-2"></i>Mis Datos Personales</h4>
                    <?= $mensaje ?>
                    
                    <form action="perfil.php" method="POST" class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">DNI (No editable)</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($usuario->getDni()) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario->getNombre()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario->getApellidos()) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario->getEmail()) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($usuario->getDireccion()) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Localidad</label>
                            <input type="text" name="localidad" class="form-control" value="<?= htmlspecialchars($usuario->getLocalidad()) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Provincia</label>
                            <input type="text" name="provincia" class="form-control" value="<?= htmlspecialchars($usuario->getProvincia()) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario->getTelefono()) ?>">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="actualizar" class="btn btn-dark w-100 rounded-pill fw-bold">GUARDAR CAMBIOS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Historial de Pedidos</h4>
                    
                    <?php if (empty($pedidos)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Aún no has realizado ningún pedido.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Código</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $p): ?>
                                    <tr>
                                        <td class="small"><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                                        <td class="fw-bold text-uppercase"><?= $p['codigo_recogida'] ?></td>
                                        <td><?= number_format($p['total'], 2) ?>€</td>
                                        <td>
                                            <span class="badge rounded-pill bg-<?= $p['estado'] == 'entregado' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($p['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            
                                            <a href="ver_pedido.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-dark">Detalles</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>