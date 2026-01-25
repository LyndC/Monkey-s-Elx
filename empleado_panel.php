<?php
// El header ya incluye la Clase Usuario y session_start
require_once 'layouts/header.php'; 
require_once 'conectar_db.php';

// Verificamos que el usuario sea empleado 
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esEmpleado()) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="card shadow-sm border-0 mb-4 bg-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-shop fs-3"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="fw-bold mb-0">Panel de Tienda</h3>
                            <p class="mb-0">Empleado: <?= htmlspecialchars($usuario->getNombre()) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 border-top border-success border-5">
                        <div class="card-body p-4 text-center">
                            <h4 class="fw-bold mb-3">Entrega de Pedidos</h4>
                            <p class="text-muted">Introduce el código de 6 caracteres proporcionado por el cliente para validar la recogida.</p>
                            
                            <form action="validar_recogida.php" method="GET" class="d-flex justify-content-center mt-4">
                                <input type="text" name="codigo" class="form-control form-control-lg w-50 me-2 text-center fw-bold" placeholder="EJ: A1B2C3" maxlength="6" style="text-transform: uppercase;">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="bi bi-search"></i> BUSCAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Pendientes</h5>
                            <p class="small text-muted">Ver lista de pedidos pagados que aún no han sido recogidos en tienda.</p>
                            <a href="pedidos_pendientes.php" class="btn btn-outline-success w-100 mt-2">Ver Listado</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Inventario</h5>
                            <p class="small text-muted">Consultar el stock actual de artículos para informar a los clientes en tienda.</p>
                            <a href="consultar_stock.php" class="btn btn-outline-success w-100 mt-2">Consultar Stock</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="logout.php" class="text-danger text-decoration-none small fw-bold">
                    <i class="bi bi-box-arrow-right"></i> CERRAR SESIÓN DE TRABAJO
                </a>
            </div>

        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>