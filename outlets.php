<?php
require_once 'conectar_db.php';
require_once 'layouts/header.php';

$pdo = conectar();

//consulta para traer solo productos con descuento, de menor a mayor
$sql = "SELECT *, (precio - (precio * descuento / 100)) AS precio_final 
        FROM articulos 
        WHERE descuento > 0 AND activo = 1 
        ORDER BY precio_final ASC";
//ejecutamos la consulta
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productosOutlet = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="bg-danger text-white p-5 rounded-3 mb-5 text-center shadow">
        <h1 class="display-4 fw-bold">🔥 ZONA OUTLET 🔥</h1>
        <p class="lead">Grandes descuentos en unidades limitadas. ¡Que no se te escapen!</p>
    </div>

    <div class="row">
        <?php if ($productosOutlet): ?>
            <?php foreach ($productosOutlet as $art): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-danger shadow-sm">
                        <span class="position-absolute top-0 start-0 badge rounded-pill bg-danger m-2 p-2">
                            -<?= $art['descuento'] ?>%
                        </span>
                        
                        <img src="<?= htmlspecialchars($art['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($art['nombre']) ?>"style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($art['nombre']) ?></h5>
                            
                            <?php 
                                $precio_original = (float)$art['precio'];
                                $precio_final = $precio_original - ($precio_original * ($art['descuento'] / 100));
                            ?>
                            <p class="text-muted mb-0"><del><?= number_format($precio_original, 2) ?>€</del></p>
                            <p class="text-danger display-6 fw-bold"><?= number_format($precio_final, 2) ?>€</p>
                            
                            <a href="articulo_detalle.php?codigo=<?= $art['codigo'] ?>" class="btn btn-danger w-100 rounded-pill">
                                Aprovechar Oferta
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h3>Actualmente no hay productos en liquidación.</h3>
                <a href="index.php" class="btn btn-outline-dark">Ver catálogo general</a>
            </div>
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