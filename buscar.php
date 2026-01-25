<?php
require_once 'conectar_db.php';
require_once 'clases/Articulo.php';
require_once 'layouts/header.php';

$busqueda = isset($_GET['codigo']) ? trim($_GET['codigo']): '';
$pdo = conectar();
$resultados = [];

//preparamos la consulta a la bd
$sql = "SELECT * FROM articulos WHERE (nombre LIKE ? OR descripcion LIKE ?) AND activo =1";
$stmt = $pdo->prepare($sql);
$stmt -> execute(["%$busqueda%", "%$busqueda%"]);
//usamos fetchAll con FETCH_CLASS para conevertir cada fila e un objeto del Artículo.
$resultados = $stmt->fetchAll(PDO::FETCH_CLASS, 'Articulo');
?>

<div class="container mt-5 mb-5" style="min-height: 60vh;">
    
    <?php if (!empty($resultados)): ?>
        <h2 class="fw-bold mb-4">Resultados para: "<?= htmlspecialchars($busqueda) ?>"</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($resultados as $art): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 text-center p-2">
                        <img src="<?= $art->getImagen() ?>" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= $art->getNombre() ?></h5>
                            <p class="text-success fw-bold fs-5"><?= number_format($art->getPrecio(), 2, ',', '.') ?>€</p>
                            <a href="articulo_detalle.php?codigo=<?= $art->getCodigo() ?>" class="btn btn-outline-dark btn-sm rounded-pill w-100">
                                Ver producto
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif (!empty($busqueda)): ?>
        <div class="text-center py-5 shadow-sm rounded border bg-white mt-4">
            <i class="bi bi-search display-1 text-muted opacity-50"></i>
            <h2 class="mt-4 fw-bold" style="color: var(--marron-texto);">¡Uy! No tenemos eso</h2>
            <p class="text-muted lead">No hemos encontrado ningún artículo que coincida con "<strong><?= htmlspecialchars($busqueda) ?></strong>"</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-warning rounded-pill px-4 fw-bold">Ver todo el catálogo</a>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-light border text-center py-4">
            <p class="mb-0">Usa la barra de búsqueda superior para encontrar productos.</p>
        </div>
    <?php endif; ?>

</div>

<?php require_once 'layouts/footer.php'; ?>