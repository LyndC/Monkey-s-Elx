<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conectar_db.php';

$mensaje = "";
$error = "";
$token = $_GET['token'] ?? ''; // Recibimos el token por la URL

// Si no hay token, redirigimos al login
if (empty($token)) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_clave = $_POST['password'];
    $confirmar_clave = $_POST['confirm_password'];

    if ($nueva_clave !== $confirmar_clave) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $pdo = conectar();
        
        // encriptamos la nueva contraseña
        $clave_encriptada = password_hash($nueva_clave, PASSWORD_DEFAULT);

        // buscamos el usuario con ese token y actualizamos la clave
        // además, borramos el token (poniéndolo a NULL) para que no se pueda usar dos veces
        $sql = "UPDATE usuarios SET clave = ?, token_recuperacion = NULL WHERE token_recuperacion = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clave_encriptada, $token]);

        if ($stmt->rowCount() > 0) {
            $mensaje = "¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.";
        } else {
            $error = "El enlace es inválido o ya ha sido utilizado.";
        }
    }
}

require 'layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body p-4 text-center">
                    <img src="logo1.png" width="100" class="mb-3" alt="Logo">
                    <h4 class="fw-bold mb-3">Nueva Contraseña</h4>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-success small py-2"><?= $mensaje ?></div>
                        <a href="login.php" class="btn btn-dark w-100 rounded-pill fw-bold py-2 mt-2">IR AL LOGIN</a>
                    <?php else: ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger small py-2"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3 text-start">
                                <label class="form-label small fw-bold">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control shadow-sm" placeholder="••••••••" required>
                            </div>
                            <div class="mb-4 text-start">
                                <label class="form-label small fw-bold">Confirmar Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control shadow-sm" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">
                                CAMBIAR CONTRASEÑA
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>