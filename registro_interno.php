<?php
require_once 'layouts/header.php';
require_once 'conectar_db.php';


if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esAdmin()) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $dni       = strtoupper(trim($_POST['dni'])); // Forzamos DNI en mayúsculas
    $nombre    = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    $localidad = htmlspecialchars(trim($_POST['localidad']));
    $provincia = htmlspecialchars(trim($_POST['provincia']));
    $telefono  = trim($_POST['telefono']);
    $email     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
   
    $clave_plana = trim($_POST['clave']);
    $clave_encriptada = password_hash($clave_plana, PASSWORD_DEFAULT);
    
    $rol       = $_POST['rol'];

    
    if (!preg_match('/^[0-9]{9,15}$/', $telefono)) {
        $mensaje = "<div class='alert alert-danger'>Error: El teléfono debe contener solo números (entre 9 y 15 dígitos).</div>";
    } elseif (!$email) {
        $mensaje = "<div class='alert alert-danger'>Error: El formato del email no es válido.</div>";
    } else {
        try {
           
            $checkDni = $pdo->prepare("SELECT dni FROM usuarios WHERE dni = ?");
            $checkDni->execute([$dni]);
            if ($checkDni->fetch()) {
                $mensaje = "<div class='alert alert-danger'>Error: Ya existe un usuario registrado con el DNI $dni.</div>";
            } else {
                
                $sql = "INSERT INTO usuarios (dni, clave, nombre, apellidos, direccion, localidad, provincia, telefono, email, rol, activo, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$dni, $clave_encriptada, $nombre, $apellidos, $direccion, $localidad, $provincia, $telefono, $email, $rol]);
                
                $mensaje = "<div class='alert alert-success'>Usuario <b>$nombre</b> creado correctamente con el rol de <b>$rol</b>.</div>";
            }
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error en la base de datos: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario Interno</h4>
                        <a href="gestionar_usuarios.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Panel
                        </a>
                    </div>

                    <?= $mensaje ?>

                    <form action="registro_interno.php" method="POST" class="row g-3">
                        <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">DNI</label>
                        <input type="text" 
                            name="dni" 
                            class="form-control" 
                            placeholder="12345678X" 
                            pattern="[0-9]{8}[A-Za-z]" 
                            maxlength="9" 
                            required>
                            <div class="invalid-feedback">
                            DNI incorrecto. El formato debe ser de 8 números seguidos de una letra.
                        </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contraseña Inicial</label>
                            <input type="password" name="clave" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Dirección</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Calle, número, piso...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Localidad</label>
                            <input type="text" name="localidad" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Provincia</label>
                            <input type="text" name="provincia" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="tel" 
                                   name="telefono" 
                                   class="form-control" 
                                   pattern="[0-9]{9,15}" 
                                   maxlength="15" 
                                   required 
                                   title="El teléfono debe contener solo números (entre 9 y 15 dígitos)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rol asignado</label>
                            <select name="rol" class="form-select border-primary fw-bold">
                                <option value="cliente">Cliente</option>
                                <option value="empleado">Empleado (Gestor)</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="registrar" class="btn btn-dark w-100 fw-bold py-2">CREAR USUARIO NUEVO</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>