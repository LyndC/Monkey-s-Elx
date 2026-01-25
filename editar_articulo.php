<?php
// "ob" significa Output Buffer (Caja de espera).
//lo activamos para que PHP guarde el HTML en una "caja" y no lo envíe al navegador todavía.
//esto nos permite usar header() para redirigir aunque ya hayamos cargado el diseño. (nos permite redireccionar despues)
ob_start();
require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php'; 
require_once 'clases/Articulo.php'; 

if (session_status() === PHP_SESSION_NONE) session_start();

//control de seguridad: Solo  para Admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

//obtenemos el código del artículo a editar desde la URL
$codigo_get = $_GET['id'] ?? ''; 

// cargamos los datos actuales del artículo
$stmtArt = $pdo->prepare("SELECT * FROM articulos WHERE codigo = ?");
$stmtArt->execute([$codigo_get]);
$datos = $stmtArt->fetch(PDO::FETCH_ASSOC);

if (!$datos) {
    die("<div class='container mt-5 alert alert-danger'>Error: El artículo no existe.</div>");
}

//creamos el objeto Articulo
$art = new Articulo(
    $datos['codigo'], 
    $datos['nombre'], 
    $datos['descripcion'], 
    $datos['categoria'],
    $datos['precio'], 
    $datos['stock'], 
    $datos['imagen'], 
    $datos['descuento'],
    $datos['activo'],
    $datos['estado'],
    
);

//obtenemos categorías para el desplegable
$stmtCat = $pdo->query("SELECT codigo, nombre FROM categorias WHERE activo = 1");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

//procesamos el formulario POST 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    $estado = $_POST['estado'];
    $descuento = isset($_POST['descuento']) ? floatval($_POST['descuento']) : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    //mantenemos la imagen actual por defecto
    $nombreImagenBD = $_POST['imagen_actual']; 
    
    // Si suben una nueva imagen, se actualiza
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = 'img/';
        $nombreArchivoOriginal = $_FILES['imagen']['name'];
        $extension = pathinfo($nombreArchivoOriginal, PATHINFO_EXTENSION);
        $nuevoNombreArchivo = time() . "_" . preg_replace("/[^A-Za-z0-9]/", "", $nombre) . "." . $extension;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $directorioDestino . $nuevoNombreArchivo)) {
            $nombreImagenBD = "img/" . $nuevoNombreArchivo;
        }
    }

    try {
        //hacemos el UPDATE usando el código como referencia
        $sql = "UPDATE articulos SET nombre=?, descripcion=?, precio=?, stock=?, categoria=?, imagen=?, activo=?, estado=?, descuento=? 
                WHERE codigo=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $nombreImagenBD, $activo, $estado, $descuento, $codigo_get]);
        
        $mensaje = "<div class='alert alert-success shadow-sm'>✅ Artículo actualizado correctamente</div>";
        
        //refrescamos los datos para que el formulario muestre lo nuevo
        header("Refresh:1; url=gestionar_articulos.php"); 
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h2>
    <a href="gestionar_articulos.php" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Panel
    </a>
</div>
                    <?= $mensaje ?>

                    <form action="editar_articulo.php?id=<?= $codigo_get ?>" method="POST" enctype="multipart/form-data" class="row g-3">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted">Código (ID)</label>
                            <input type="text" class="form-control bg-light" value="<?= $art->getCodigo() ?>" readonly>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($art->getNombre()) ?>" required>
                        </div>

                        <div class="col-md-6">
    <label class="form-label small fw-bold">Categoría</label>
    <select name="categoria" class="form-select" required>
        <option value="">-- Selecciona una categoría --</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['codigo'] ?>" <?= ($art->getCategoria() == $cat['codigo']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <div class="form-text text-muted small">Solo aparecen categorías activas.</div>
</div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($art->getDescripcion()) ?></textarea>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Precio (€)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" value="<?= $art->getPrecio() ?>" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Descuento (%)</label>
                            <input type="number" name="descuento" class="form-control" value="<?= $art->getDescuento() ?>" min="0" max="100">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?= $art->getStock() ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="nuevo" <?= $art->getEstado() == 'nuevo' ? 'selected' : '' ?>>Nuevo</option>
                                <option value="reutilizado" <?= $art->getEstado() == 'reutilizado' ? 'selected' : '' ?>>Reutilizado</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Imagen actual</label>
                            <div class="mb-2">
                                <img src="<?= $art->getImagen() ?>" class="img-thumbnail" width="120">
                            </div>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <input type="hidden" name="imagen_actual" value="<?= $art->getImagen() ?>">
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="activo" id="checkActivo" <?= $art->getActivo() ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="checkActivo">Artículo visible en tienda</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold">GUARDAR CAMBIOS</button>
                            <a href="gestionar_articulos.php" class="btn btn-outline-secondary px-5 rounded-pill">CANCELAR</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>