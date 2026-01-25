<?php
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php'; 

//control de acceso o seguridad
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esAdmin()) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";
$dni_a_editar = $_GET['dni'] ?? '';
$admin_actual = $_SESSION['usuario']->getDni();

if ($dni_a_editar === $admin_actual) {
    header("Location: gestionar_usuarios.php?error=autoedit_prohibido");
    exit;
}

// lógica para actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $nombre    = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    $localidad = htmlspecialchars(trim($_POST['localidad']));
    $provincia = htmlspecialchars(trim($_POST['provincia']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $email     = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $rol       = $_POST['rol'];
    $activo    = isset($_POST['activo']) ? 1 : 0;

    try {
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, direccion=?, localidad=?, provincia=?, telefono=?, email=?, rol=?, activo=? WHERE dni=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellidos,$direccion, $localidad, $provincia, $telefono, $email, $rol, $activo, $dni_a_editar]);
        $mensaje = "<div class='alert alert-success'>Usuario actualizado con éxito.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

//cargamos los datos y creamos el objeto
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni = ?");
$stmt->execute([$dni_a_editar]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datos) { die("Usuario no encontrado."); }

//transformamos el array en Objeto Usuario
$u = new Usuario(
    $datos['dni'],
    $datos['clave'],
    $datos['nombre'],
    $datos['apellidos'],
    $datos['direccion'],      
    $datos['localidad'],      
    $datos['provincia'],
    $datos['telefono'],
    $datos['email'],
    $datos['rol'],
    $datos['fecha_registro'],
    $datos['activo']
);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h4>
        <a href="gestionar_usuarios.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Panel
        </a>
    </div>
                    
                    <?= $mensaje ?>

                    <form action="editar_usuario.php?dni=<?= htmlspecialchars($dni_a_editar) ?>" method="POST" class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">DNI (Identificador único)</label>
                            <input type="text" class="form-control bg-light" value="<?= ($u->getDni()) ?>" readonly>
                        </div>

                        <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small fw-bold">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($u->getNombre()) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Apellidos</label>
        <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($u->getApellidos()) ?>" required>
    </div>

    <div class="col-md-8">
        <label class="form-label small fw-bold">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($u->getDireccion()) ?>">
    </div>

    <div class="col-md-8">
        <label class="form-label small fw-bold">Localidad</label>
        <input type="text" name="localidad" class="form-control" value="<?= htmlspecialchars($u->getLocalidad()) ?>">
    </div>

    <div class="col-md-8">
        <label class="form-label small fw-bold">Provinvia</label>
        <input type="text" name="provincia" class="form-control" value="<?= htmlspecialchars($u->getProvincia()) ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($u->getTelefono()) ?>">
    </div>

    <div class="col-12">
        <label class="form-label small fw-bold">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u->getEmail()) ?>" required>
    </div>
    
    </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Rol del Sistema</label>
                            <select name="rol" class="form-select border-primary fw-bold">
                                <option value="cliente" <?= $u->getRol() == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                <option value="empleado" <?= $u->getRol() == 'empleado' ? 'selected' : '' ?>>Empleado (Gestor)</option>
                                <option value="admin" <?= $u->getRol() == 'admin' ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-switch" type="checkbox" name="activo" id="checkActivo" <?= $u->getActivo() ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-bold" for="checkActivo">Usuario Activo</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 d-flex gap-2">
                            <button type="submit" name="guardar" class="btn btn-dark w-100 fw-bold py-2">GUARDAR CAMBIOS</button>
                            <a href="gestionar_usuarios.php" class="btn btn-outline-secondary w-100 fw-bold py-2">CANCELAR</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>