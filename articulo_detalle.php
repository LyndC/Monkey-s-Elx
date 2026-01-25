<?php
require_once 'clases/Articulo.php';
require 'conectar_db.php';

$pdo = conectar();

//capturamos el código de la URL
$codigo = $_GET['codigo'] ?? '';

//buscamos el artículo en la base de datos
$stmt = $pdo->prepare("SELECT * FROM articulos WHERE codigo = ? AND activo = 1");
$stmt->execute([$codigo]);

//usamos FETCH_CLASS para tener un objeto Articulo
$articulo = $stmt->fetchObject('Articulo');

//si no existe el artículo, redirigimos al index
if (!$articulo) {
    header("Location: index.php");
    exit;
}
//aplicamos lógica para el descuento
$precioOriginal = $articulo->getPrecio();
$porcentajeDesc = $articulo->getDescuento();
$precioFinal = $precioOriginal;

if ($porcentajeDesc > 0) {
    $ahorro = $precioOriginal * ($porcentajeDesc / 100);
    $precioFinal = $precioOriginal - $ahorro;
}
require 'layouts/header.php';
?>

<div class="container mt-5 mb-5">
<div class="mb-4">
        <a href="index.php" 
           class="btn btn-light border-secondary-subtle text-secondary rounded-pill px-4 shadow-sm" 
           style="background-color: #fff; border: 1px solid #ced4da; display: inline-flex; align-items: center;">
            <i class="bi bi-arrow-left me-2"></i> Volver al inicio
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 text-center">
            <img src="<?= $articulo->getImagen() ?>" class="img-fluid rounded shadow-sm" alt="<?= $articulo->getNombre() ?>" style="max-height: 500px;">
        </div>

        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-dark">Tienda</a></li>
                    <li class="breadcrumb-item active"><?= $articulo->getEstado() ?></li>
                </ol>
            </nav>

            <h1 class="fw-bold display-5"><?= $articulo->getNombre() ?></h1>
            <p class="badge <?= $articulo->getEstado() === 'nuevo' ? 'bg-success' : 'bg-info text-dark' ?> fs-6">
                <?= ucfirst($articulo->getEstado()) ?>
            </p>
            
            <div class="mt-3">
        <?php if ($porcentajeDesc > 0): ?>
        <span class="text-muted text-decoration-line-through fs-4 me-2">
            <?= number_format($precioOriginal, 2) ?>€
        </span>
        <span class="badge bg-danger mb-2">¡OFERTA -<?= $porcentajeDesc ?>%!</span>
        
        <h2 class="text-danger fw-bold display-6">
            <?= number_format($precioFinal, 2) ?>€
        </h2>
        <?php else: ?>
        <h3 class="text-dark fw-bold mt-3">
            <?= $articulo->getPrecioFormateado() ?>
        </h3>
    <?php endif; ?>
    </div>
            
            <hr>
            
            <h5>Descripción</h5>
            <p class="text-muted"><?= $articulo->getDescripcion() ?></p>
            
            <div class="mt-4">
                <p class="fw-bold">Stock disponible: <span class="badge bg-light text-dark border"><?= $articulo->getStock() ?> unidades</span></p>
                
               <?php if ($articulo->hayStock()): ?>
        <a href="carrito_agregar.php?codigo=<?= $articulo->getCodigo() ?>" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm">
        <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
        </a>
        <?php else: ?>
        <button class="btn btn-secondary btn-lg rounded-pill px-5" disabled>Sin stock</button>
        <?php endif; ?>
            
            </div>
        </div>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>