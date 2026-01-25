<?php
// "ob" significa Output Buffer (Caja de espera).
//lo activamos para que PHP guarde el HTML en una "caja" y no lo envíe al navegador todavía.
//esto nos permite usar header() para redirigir aunque ya hayamos cargado el diseño.(nos permite redireccionar despues)
ob_start();

require_once 'layouts/header.php';
require_once 'conectar_db.php';
require_once 'clases/Usuario.php'; 
require_once 'clases/Articulo.php'; 

if (session_status() === PHP_SESSION_NONE) session_start();

// Control de seguridad solo para el Admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = conectar();
$mensaje = "";

//obtenemos categorías para el desplegable
$stmtCat = $pdo->query("SELECT codigo, nombre FROM categorias WHERE activo = 1");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

//procesamos el formulario post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //recogemos los datos manteniendo el orden de la clase articulo
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    $estado = $_POST['estado'];
    $descuento = isset($_POST['descuento']) ? floatval($_POST['descuento']) : 0;
    
    //por defecto no-foto
    $nombreImagenBD = "img/no-foto.png"; 
    //se sube la imagen del pc al servidor, cambia de nombre y lo guarda en /img. La BD guarda solo el texto(nombre)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = 'img/';
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nuevoNombreArchivo = time() . "_" . preg_replace("/[^A-Za-z0-9]/", "", $nombre) . "." . $extension;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $directorioDestino . $nuevoNombreArchivo)) {
            $nombreImagenBD = "img/" . $nuevoNombreArchivo;
        }
    }

    //generación del código
    //busca el numero mas alto(max), limpia art y queda el numero, le suma 1 y luego se le vuelve a poner el prefijo art
    $queryMax = $pdo->query("SELECT MAX(CAST(SUBSTRING(codigo, 4) AS UNSIGNED)) as ultimo_num FROM articulos");
    $resultado = $queryMax->fetch(PDO::FETCH_ASSOC);
    $nuevoNumero = ($resultado['ultimo_num'] ?? 0) + 1;
    
    //bucle de seguridad para evitar el error "Duplicate entry" entrada duplicada
    $codigoExiste = true;
    while ($codigoExiste) {
        $codigoGenerado = "ART" . str_pad($nuevoNumero, 4, "0", STR_PAD_LEFT);
        $check = $pdo->prepare("SELECT COUNT(*) FROM articulos WHERE codigo = ?");
        $check->execute([$codigoGenerado]);
        
        if ($check->fetchColumn() == 0) {
            $codigoExiste = false;
        } else {
            $nuevoNumero++;
        }
    }

    // insertamos en tabla articulos
    try {
        $sql = "INSERT INTO articulos (codigo, nombre, descripcion, precio, stock, categoria, imagen, activo, estado, descuento) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Ejecutamos pasando las variables en el orden de los "?"
        $stmt->execute([
            $codigoGenerado, 
            $nombre, 
            $descripcion, 
            $precio, 
            $stock, 
            $categoria, 
            $nombreImagenBD, 
            $estado, 
            $descuento
        ]);
        
        $mensaje = "<div class='alert alert-success shadow-sm'>✅ Artículo creado correctamente con código $codigoGenerado</div>";
        header("Refresh:2; url=gestionar_articulos.php"); 

    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al guardar: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold m-0"><i class="bi bi-plus-circle me-2"></i>Añadir Producto</h2>
                        <a href="gestionar_articulos.php" class="btn btn-outline-secondary shadow-sm">
                            <i class="bi bi-arrow-left"></i> Volver al Panel
                        </a>
                    </div>

                    <?= $mensaje ?>

                    <form action="nuevo_articulo.php" method="POST" enctype="multipart/form-data" class="row g-3">
                        
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Monitor Gaming" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="categoria" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['codigo'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles del artículo..."></textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Precio (€)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Descuento (%)</label>
                            <input type="number" name="descuento" class="form-control" value="0" min="0" max="100">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Stock Inicial</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="nuevo">Nuevo</option>
                                <option value="reutilizado">Reutilizado</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Imagen del Producto</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <div class="form-text text-muted">Si no subes ninguna, se asignará una por defecto.</div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex gap-3">
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold">
                                <i class="bi bi-save me-2"></i>GUARDAR ARTÍCULO
                            </button>
                            <a href="gestionar_articulos.php" class="btn btn-light px-5 rounded-pill border">CANCELAR</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>