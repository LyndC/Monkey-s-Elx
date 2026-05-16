<?php
require_once 'layouts/header.php'; 
require_once 'conectar_db.php';

if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esAdmin()) {
    header("Location: index.php");
    exit;
}
$usuario = $_SESSION['usuario'];
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-10">
            
            <div class="card shadow-sm border-0 mb-4 bg-dark text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-badge-fill fs-3"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h3 class="fw-bold mb-0">Administración Central</h3>
                            <p class="mb-0 text-light opacity-75">Bienvenido, <?= htmlspecialchars($usuario->getNombre()) ?> | Nivel: Superusuario</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <h5 class="fw-bold mb-3 text-muted uppercase small"><i class="bi bi-people-fill me-2"></i>Recursos Humanos y Clientes</h5>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100 border-start border-primary border-5">
                        <div class="card-body">
                            <h5 class="fw-bold mb-2">Gestión de Usuarios</h5>
                            <p class="small text-muted">CRUD de Empleados y Clientes. Altas, bajas lógicas y cambio de roles.</p>
                            <a href="gestionar_usuarios.php" class="btn btn-primary btn-sm">Acceder al listado</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100 border-start border-info border-5">
                        <div class="card-body">
                            <h5 class="fw-bold mb-2">Mi Perfil Personal</h5>
                            <p class="small text-muted">Modifica tus datos personales y consulta tu propio historial de pedidos.</p>
                            <a href="perfil.php" class="btn btn-info btn-sm text-white">Editar mi perfil</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <h5 class="fw-bold mb-3 text-muted uppercase small"><i class="bi bi-box-seam-fill me-2"></i>Operaciones de Tienda</h5>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100 text-center p-3">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <i class="bi bi-tags fs-1 text-success mb-2"></i>
                                <h6 class="fw-bold">Productos y Categorías</h6>
                                <p class="x-small text-muted mb-3">Control de stock, precios y organización de familias.</p>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="gestionar_articulos.php" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-box-seam me-1"></i> Ver Productos
                                </a>
                                <a href="gestionar_categorias.php" class="btn btn-primary btn-sm">
                                    <i class="bi bi-bookmark-plus me-1"></i> Crear Categorías
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100 text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-cart-check fs-1 text-warning mb-2"></i>
                            <h6 class="fw-bold">Pedidos Globales</h6>
                            <p class="x-small text-muted mb-3">Ver todos los pedidos del sistema y actualizar estados.</p>
                            <a href="gestionar_pedidos.php" class="btn btn-outline-warning btn-sm w-100">Ver pedidos</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100 text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-bar-chart-line fs-1 text-danger mb-2"></i>
                            <h6 class="fw-bold">Estadísticas e Informes</h6>
                            <p class="x-small text-muted mb-3">Análisis de ventas, productos más vendidos y registros.</p>
                            <a href="reportes.php" class="btn btn-outline-danger btn-sm w-100">Ver Reportes</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <hr class="mb-4">
                <a href="logout.php" class="btn btn-link text-danger text-decoration-none fw-bold">
                    <i class="bi bi-power"></i> FINALIZAR SESIÓN ADMINISTRATIVA
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.85rem; }
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-3px); }
</style>

<?php require_once 'layouts/footer.php'; ?>