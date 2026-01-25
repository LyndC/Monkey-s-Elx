<?php
require_once 'clases/Articulo.php';
require_once 'conectar_db.php';
require 'layouts/header.php'; 

$total_carrito = 0;
$pdo = conectar();
?>

<div class="container mt-5 mb-5">
    <h2 class="fw-bold mb-4" style="color: var(--marron-texto);">
        <i class="bi bi-cart3 me-2"></i> Mi Carrito
    </h2>

    <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table align-middle bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unit.</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($_SESSION['carrito'] as $codigo => $cantidad): 
                        $stmt = $pdo->prepare("SELECT * FROM articulos WHERE codigo = ?");
                        $stmt->execute([$codigo]);
                        // Usamos fetch(PDO::FETCH_ASSOC) para manejar mejor el campo 'descuento' 
                        $art_data = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($art_data):
                            // lógica para el descuento
                            $precio_base = $art_data['precio'];
                            $porcentaje_desc = $art_data['descuento'] ?? 0;
                            $precio_con_descuento = $precio_base * (1 - ($porcentaje_desc / 100));
                            
                            $subtotal = $precio_con_descuento * $cantidad;
                            $total_carrito += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= $art_data['imagen'] ?>" alt="" style="width: 60px; height: 60px; object-fit: cover;" class="rounded me-3">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($art_data['nombre']) ?></div>
                                        <?php if ($porcentaje_desc > 0): ?>
                                            <span class="badge bg-danger">Oferta -<?= $porcentaje_desc ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($porcentaje_desc > 0): ?>
                                    <small class="text-decoration-line-through text-muted"><?= number_format($precio_base, 2, ',', '.') ?>€</small><br>
                                    <span class="text-success fw-bold"><?= number_format($precio_con_descuento, 2, ',', '.') ?>€</span>
                                <?php else: ?>
                                    <?= number_format($precio_base, 2, ',', '.') ?>€
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group border rounded-pill overflow-hidden">
                                    <a href="carrito_accion.php?action=decrementar&cod=<?= $codigo ?>" class="btn btn-sm btn-light">-</a>
                                    <span class="px-3 py-1 bg-white"><?= $cantidad ?></span>
                                    <a href="carrito_accion.php?action=incrementar&cod=<?= $codigo ?>" class="btn btn-sm btn-light">+</a>
                                </div>
                            </td>
                            <td class="fw-bold"><?= number_format($subtotal, 2, ',', '.') ?> €</td>
                            <td class="text-center">
                                <a href="carrito_accion.php?action=eliminar&cod=<?= $codigo ?>" class="text-danger">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <a href="index.php" class="btn btn-outline-dark rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Seguir comprando
                </a>
            </div>
            <div class="col-md-6 text-end">
                <div class="bg-light p-4 rounded border mb-3 shadow-sm">
                    <h5 class="text-muted mb-2">Resumen de recogida</h5>
                    <h3 class="mb-0">Total a pagar: <span class="text-primary"><?= number_format($total_carrito, 2, ',', '.') ?> €</span></h3>
                    <small class="text-muted">IVA incluido - Recogida en tienda</small>
                </div>
                
                <div class="d-grid gap-2 d-md-block">
                    <a href="checkout_stripe.php" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
                        Pagar ahora <i class="bi bi-stripe ms-2"></i>
                    </a>
                    <a href="confirmar_pedido.php" class="btn btn-success btn-lg rounded-pill px-4 fw-bold">
                        Pagar al recoger <i class="bi bi-shop ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5 shadow-sm rounded bg-light border">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <p class="lead mt-3">Tu carrito está vacío.</p>
            <a href="index.php" class="btn btn-dark rounded-pill px-4">Volver a la tienda</a>
        </div>
    <?php endif; ?>
</div>

<?php require 'layouts/footer.php'; ?>