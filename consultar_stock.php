<?php
require_once 'clases/Articulo.php';
require_once 'layouts/header.php';
require_once 'conectar_db.php';

$pdo = conectar();

//obtenemos todos los artículos activos
$sql = "SELECT * FROM articulos WHERE activo = 1 ORDER BY stock ASC";
$stmt = $pdo->query($sql);
$articulos = $stmt->fetchAll(PDO::FETCH_CLASS, 'Articulo');
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-clipboard-data me-2"></i>Control de Inventario</h2>
        <span class="badge bg-dark rounded-pill px-3 py-2">Total: <?= count($articulos) ?> productos</span>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php foreach ($articulos as $art): 
            //lógica de colores para el stock
            $stock = $art->getStock();
            $cardClass = "border-start border-4 ";
            $badgeClass = "bg-success";
            
            if ($stock <= 0) {
                $cardClass .= "border-danger";
                $badgeClass = "bg-danger";
            } elseif ($stock <= 5) {
                $cardClass .= "border-warning";
                $badgeClass = "bg-warning text-dark";
            } else {
                $cardClass .= "border-success";
            }
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 <?= $cardClass ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title fw-bold mb-0"><?= $art->getNombre() ?></h6>
                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= $stock ?></span>
                        </div>
                        <p class="text-muted small mb-3">Ref: <?= $art->getCodigo() ?></p>
                        
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar <?= $badgeClass ?>" role="progressbar" 
                                 style="width: <?= min(($stock / 20) * 100, 100) ?>%"></div>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-between">
                            <span class="fw-bold text-primary"><?= number_format($art->getPrecio(), 2) ?>€</span>
                            <a href="editar_stock.php?codigo=<?= $art->getCodigo() ?>" class="text-secondary">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>