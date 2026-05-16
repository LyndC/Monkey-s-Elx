<?php 
//crgamos la clase articulo
require_once 'clases/Articulo.php';
//definimos la cabecera, como ya se llamó en este archivo a la clase usuario.php
//se carga automaticamente a llamar a header.php, y también se hace sessio_start desde allí
//si pongo session_start aqui, daría error por duplicidad de código
require 'layouts/header.php';
require 'conectar_db.php';
$pdo = conectar();
//aquí iniciamos la lógica para la paginación
$articulos_por_pagina = 6;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$inicio = ($pagina_actual - 1) * $articulos_por_pagina;

// Contamos el total de artículos activos para saber cuántas páginas hay
$total_articulos = $pdo->query("SELECT COUNT(*) FROM articulos WHERE activo = 1 AND stock > 0")->fetchColumn();
$total_paginas = ceil($total_articulos / $articulos_por_pagina);

//hacemos la consulta con LIMIT y OFFSET
$stmt = $pdo->prepare("SELECT * FROM articulos WHERE activo = 1 AND stock > 0 LIMIT :inicio, :cantidad");
$stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
$stmt->bindValue(':cantidad', (int)$articulos_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$articulos = $stmt->fetchAll(PDO::FETCH_CLASS, 'Articulo');     
?>



<?php

if (function_exists('conectar') && !isset($pdo)) {
    $pdo = conectar();
}


$categorias_nav = [];
if (isset($pdo)) {
    try {
        $consulta_bar = $pdo->query("SELECT codigo, nombre FROM categorias WHERE activo = 1 ORDER BY nombre ASC");
        $categorias_nav = $consulta_bar->fetchAll();
    } catch (PDOException $e) {
        $categorias_nav = []; // Evitamos colgar la página si falla la BD
    }
}


$iconos_categorias = [
    1 => 'bi-cpu',                  // Electrónica
    2 => 'bi-controller',           // Ocio
    3 => 'bi-handbag',              // Moda y Accesorios
    4 => 'bi-trophy',               // Deporte
    5 => 'bi-music-note-beamed',    // Música e Instrumentos / Cine
    6 => 'bi-plug'                  // Electrodomésticos
];
?>

<nav class="category-bar">
    <div class="container">
        <ul class="nav justify-content-center">
            <?php if (!empty($categorias_nav)): ?>
                <?php foreach ($categorias_nav as $cat): 
                    // Si el código existe en nuestro diccionario usa su icono, si no, le pone una etiqueta genérica
                    $id_cat = $cat['codigo'];
                    $icono = isset($iconos_categorias[$id_cat]) ? $iconos_categorias[$id_cat] : 'bi-tag';
                ?>
                    <li class="nav-item">
                        <a class="nav-link category-link" href="categorias.php?codigo=<?= $cat['codigo'] ?>">
                            <i class="bi <?= $icono ?> me-1"></i> <?= htmlspecialchars($cat['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link category-link text-danger" href="outlets.php">
                    <i class="bi bi-percent me-1"></i> Ofertas
                </a>
            </li>
        </ul>
    </div>
</nav>
    <!--Carousel-->

    <!--Parallax-->
<div class="parallax-window d-flex align-items-center justify-content-center text-white">
    <div class="text-center p-3 bg-dark bg-opacity-25 rounded-4" style="backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.1);">
        <h4 class="fw-bold mb-1" style="letter-spacing: 2px; font-size: 1.2rem;">ECOSISTEMA MONKEY'S</h4>
        <p class="lead mb-3" style="font-style: italic;">"Innovación responsable: estrena o reutiliza"</p>
        
        <a href="#productos" class="btn btn-warning btn-sm rounded-pill fw-bold px-4 py-2 shadow-sm">
            Explorar Catálogo
        </a>
    </div>
</div>
<!--h2-->

<div class="container mt-5 mb-4">
    <div class="text-center">
        <span class="badge bg-rosa-principal text-dark rounded-pill px-3 mb-2 fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Top Selección</span>
        
        <h2 class="fw-bold display-6" style="color: var(--marron-texto);">LOS FAVORITOS DEL MES</h2>
        
        <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: var(--rosa-fuerte); border-radius: 2px;"></div>
    </div>
</div>


    <!--Cards-->
<div class="row justify-content-center mx-2">
    <?php foreach ($articulos as $art): ?>
    <?php 
        // Lógica de precio para el index
        $precioOriginal = $art->getPrecio();
        $porcentajeDesc = $art->getDescuento();
        $hayDescuento = $porcentajeDesc > 0;
        $precioFinal = $hayDescuento ? $precioOriginal - ($precioOriginal * ($porcentajeDesc / 100)) : $precioOriginal;
    ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm text-center border-0 position-relative">
            
            <div class="position-absolute top-0 start-0 m-2">
                <?php if ($art->getEstado() === 'nuevo'): ?> 
                    <span class="badge bg-success">Nuevo</span>
                <?php else: ?>
                    <span class="badge bg-info text-dark">Reutilizado</span>
                <?php endif; ?>
            </div>

            <?php if ($hayDescuento): ?>
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-danger shadow-sm">-<?= $porcentajeDesc ?>%</span>
                </div>
            <?php endif; ?>

            <img src="<?= $art->getImagen() ?>" class="card-img-top p-3" alt="<?= $art->getNombre() ?>" style="height: 250px; object-fit: contain;">
            
            <div class="card-body d-flex flex-column">
                <h5 class="fw-bold"><?= $art->getNombre() ?></h5>
                <p class="card-text small text-muted flex-grow-1"><?= substr($art->getDescripcion(), 0, 80) ?>...</p>
                
                <div class="mb-3">
                    <?php if ($hayDescuento): ?>
                        <span class="text-muted text-decoration-line-through small"><?= number_format($precioOriginal, 2) ?>€</span>
                        <p class="fw-bold fs-4 text-danger m-0"><?= number_format($precioFinal, 2) ?>€</p>
                    <?php else: ?>
                        <p class="fw-bold fs-4 m-0"><?= $art->getPrecioFormateado() ?></p>
                    <?php endif; ?>
                </div>
                
                <a href="articulo_detalle.php?codigo=<?= $art->getCodigo() ?>" class="btn btn-outline-dark rounded-pill w-100">Ver detalles</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<div class="container mt-4 mb-5">
    <nav aria-label="Navegación de productos">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm text-dark rounded-circle mx-1" href="?p=<?= $pagina_actual - 1 ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= ($pagina_actual == $i) ? 'active' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle <?= ($pagina_actual == $i) ? 'bg-dark text-white' : 'text-dark' ?>" 
                       href="?p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm text-dark rounded-circle mx-1" href="?p=<?= $pagina_actual + 1 ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>

        </ul>
    </nav>
</div>

<!--CTA-->
<section class="bg-light text-center p-5">
  <h2 class="fw-bold">Experiencia Monkey's</h2>
  <p>Únete a nuestra comunidad y disfruta de ofertas exclusivas</p>
  <a href="carrito.php" class="btn btn-dark btn-lg">Compra Ahora</a>
</section>
<!--Sostenibilidad y objetivo de la tienda-->
<section class="py-4 border-top bg-light"> <div class="container text-center">
        <p class="mb-0" style="font-size: 0.85rem; color: #555; line-height: 1.2;">
            <i class="bi bi-arrow-repeat text-success me-2"></i> 
            En <strong>Monkey's</strong> creemos en un planeta sostenible: 
            <span class="text-success">reduce, reutiliza y recicla</span>. 
            Y cuando decidas <span class="text-primary">innovar</span>, hazlo con nosotros de forma consciente.
        </p>
    </div>
</section>

<?php require 'layouts/footer.php'; ?>
