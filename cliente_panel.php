<?php
//cargamos la clase antes de nada
require_once 'clases/Usuario.php'; 

//argamos el header que ya tiene el sesión_start
require_once 'layouts/header.php'; 

// verificamos sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// extraemos el objeto
$usuario = $_SESSION['usuario'];

//prueba de seguridad
$nombreUser = "";
if (method_exists($usuario, 'getNombre')) {
    $nombreUser = $usuario->getNombre();
} else {
    //intentamos acceder a la propiedad directamente(código defensivo)
    //con gentNombre o sin getNombre
    $nombreUser = $usuario->nombre ?? 'Usuario'; 
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-fill fs-3"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="fw-bold mb-0">¡Hola, <?= htmlspecialchars($usuario->getNombre()) ?>!</h3>
                            <p class="text-muted mb-0">Bienvenido a tu área personal de Monkey's</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Mis Datos</h5>
                            <hr>
                            <p class="small mb-1 text-muted">DNI / Documento:</p>
                            <p class="fw-bold"><?= htmlspecialchars($usuario->getDni()) ?></p>
                            
                            <p class="small mb-1 text-muted">Correo Electrónico:</p>
                            <p class="fw-bold"><?= htmlspecialchars($usuario->getEmail()) ?></p>
                            
                            <p class="small mb-1 text-muted">Teléfono:</p>
                            <p class="fw-bold"><?= htmlspecialchars($usuario->getTelefono() ?? 'No indicado') ?></p>

                            <a href="editar_perfil.php" class="btn btn-outline-dark btn-sm w-100 mt-3">Editar Perfil</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <h5 class="fw-bold mb-4 text-center">¿Qué quieres hacer hoy?</h5>
                            
                            <div class="d-grid gap-3">
                                <a href="mis_pedidos.php" class="btn btn-dark py-3 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-qr-code-scan me-2"></i> MIS PEDIDOS Y CÓDIGOS
                                </a>

                                <a href="index.php" class="btn btn-outline-secondary py-3 rounded-pill">
                                    <i class="bi bi-shop me-2"></i> VOLVER A LA TIENDA
                                </a>

                                <a href="logout.php" class="btn btn-link text-danger mt-3 text-decoration-none">
                                    Cerrar sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>