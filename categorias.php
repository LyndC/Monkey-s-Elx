<?php
require_once 'conectar_db.php';


$pdo = conectar();
$idCategoria = $_GET['codigo'] ?? null;
$esOferta = $_GET['ofertas'] ?? null;


// Construimos la consulta según lo que el usuario haya pulsado
if ($esOferta) {
    $sql = "SELECT * FROM articulos WHERE descuento > 0 AND activo = 1";
    $titulo = "Grandes Ofertas";
    $params = [];
} else {
    $sql = "SELECT * FROM articulos WHERE categoria = ? AND activo = 1";
    $params = [$idCategoria];

    //catgorias dinamica que faltaban 
    $sqlcat = "SELECT nombre FROM categorias WHERE codigo = ? and activo = 1";
    $stmtcat = $pdo->prepare($sqlcat);
    $stmtcat ->execute([$idCategoria]);
    $categoria = $stmtcat->fetch(PDO::FETCH_ASSOC);
    // Opcional: Obtener el nombre de la categoría para el título
    $titulo = $categoria ? $categoria['nombre'] : "Productos de la Categoría";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'layouts/header.php';  
?>

<div class="container mt-5">
    <h2 class="mb-4 text-uppercase fw-bold"><?= $titulo ?></h2>
    <div class="row">
        <?php if (count($articulos) > 0): ?>
            <?php foreach ($articulos as $art): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($art['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($art['nombre']) ?>"style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= $art['nombre'] ?></h5>
                            <?php if ($art['descuento'] > 0): ?>
        <?php 
            $precioOriginal = (float)$art['precio'];
            $precioFinal = $precioOriginal - ($precioOriginal * ($art['descuento'] / 100));
        ?>
        <p class="mb-0 text-muted"><del><?= number_format($precioOriginal, 2) ?>€</del></p>
        <p class="text-danger fw-bold fs-5"><?= number_format($precioFinal, 2) ?>€</p>
    <?php else: ?>
        <p class="text-primary fw-bold fs-5"><?= number_format($art['precio'], 2) ?>€</p>
    <?php endif; ?>
        <a href="articulo_detalle.php?codigo=<?= $art['codigo'] ?>" class="btn btn-outline-dark">Ver producto</a>
            </div>
        </div>
    </div>
        <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No hay productos disponibles en esta sección actualmente.</div>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-4 mb-2">
    <a href="index.php" 
       class="btn shadow-sm" 
       style="background-color: #ffffff; border: 1px solid #dee2e6; color: #495057; display: inline-flex; align-items: center; text-decoration: none; border-radius: 4px; padding: 6px 12px; transition: 0.3s;">
        <i class="bi bi-arrow-left me-2"></i> Volver al inicio
    </a>
</div>

<?php require_once 'layouts/footer.php'; ?>