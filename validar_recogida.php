<?php
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// control de seguridad
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']->getRol() !== 'empleado' && $_SESSION['usuario']->getRol() !== 'admin')) {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$pedido = null;
$lineas = [];
$mensaje = "";

// lógica para la entrega (Procesamos esto antes de la búsqueda para mostrar el resultado)
if (isset($_POST['confirmar_entrega'])) {
    $idPed = $_POST['id_pedido'];
    $dniIntroducido = strtoupper(trim($_POST['dni_confirmacion']));
    $dniEsperado = strtoupper(trim($_POST['dni_esperado']));

    // validamos el dni para poder hacer la entrega del artículo
    if ($dniIntroducido === $dniEsperado) {
        $stmtU = $pdo->prepare("UPDATE pedidos SET estado = 'entregado', fecha_recogida_real = NOW() WHERE idPedido = ?");
        if ($stmtU->execute([$idPed])) {
            $mensaje = "<div class='alert alert-success shadow'>✅ <strong>Identidad Verificada:</strong> Pedido #$idPed entregado con éxito.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger shadow'>❌ <strong>Error de Seguridad:</strong> El DNI introducido no coincide con el titular del pedido.</div>";
    }
}

// lógica para buscar el código de recogida
if (isset($_POST['buscar_codigo']) || (isset($_POST['confirmar_entrega']) && $pedido == null)) {
    $codigo = strtoupper(trim($_POST['codigo_busqueda'] ?? ''));
    
    if ($codigo) {
        $stmt = $pdo->prepare("SELECT p.*, u.nombre as cliente_nombre 
                               FROM pedidos p 
                               JOIN usuarios u ON p.codUsuario = u.dni 
                               WHERE p.codigo_recogida = ? AND p.activo = 1");
        $stmt->execute([$codigo]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            $stmtL = $pdo->prepare("SELECT lp.*, a.nombre as nombre_art 
                                    FROM lineapedido lp 
                                    JOIN articulos a ON lp.codArticulo = a.codigo 
                                    WHERE lp.numPedido = ?");
            $stmtL->execute([$pedido['idPedido']]);
            $lineas = $stmtL->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4 text-primary"><i class="bi bi-shield-check"></i> Validación de Recogida</h2>
            
            <?php echo $mensaje; ?>

            <div class="card mb-4 border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <form method="POST" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Paso 1: Introducir Código de 6 dígitos</label>
                            <input type="text" name="codigo_busqueda" class="form-control form-control-lg" 
                                   value="<?php echo $_POST['codigo_busqueda'] ?? ''; ?>" placeholder="Ej: A1B2C3" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" name="buscar_codigo" class="btn btn-primary btn-lg w-100">Buscar Pedido</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($pedido): ?>
                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Pedido #<?php echo $pedido['idPedido']; ?> - Cliente: <?php echo $pedido['cliente_nombre']; ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">DNI Registrado:</p>
                                <p class="h5">****<?php echo substr($pedido['codUsuario'], -4); ?> (Oculto por seguridad)</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1 text-muted">Estado Actual:</p>
                                <span class="badge bg-warning text-dark fs-6"><?php echo strtoupper($pedido['estado']); ?></span>
                            </div>
                        </div>

                        <?php if ($pedido['estado'] !== 'entregado'): ?>
                            <div class="bg-warning bg-opacity-10 p-4 rounded border border-warning mb-4">
                                <form method="POST">
                                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['idPedido']; ?>">
                                    <input type="hidden" name="dni_esperado" value="<?php echo $pedido['codUsuario']; ?>">
                                    <input type="hidden" name="codigo_busqueda" value="<?php echo $pedido['codigo_recogida']; ?>">
                                    
                                    <label class="form-label fw-bold">Paso 2: Confirmar DNI del cliente para entregar</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                        <input type="text" name="dni_confirmacion" class="form-control form-control-lg" 
                                               placeholder="Escriba el DNI completo" required>
                                    </div>
                                    <button type="submit" name="confirmar_entrega" class="btn btn-success btn-lg w-100">
                                        VERIFICAR Y ENTREGAR PRODUCTOS
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Artículo</th>
                                    <th class="text-center">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lineas as $l): ?>
                                <tr>
                                    <td><?php echo $l['nombre_art']; ?></td>
                                    <td class="text-center">x<?php echo $l['cantidad']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>