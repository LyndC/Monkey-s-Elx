<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conectar_db.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pdo = conectar();

    //verificamos en la BD si el email esta registrado
    $stmt = $pdo->prepare("SELECT dni FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        //generamos un token único
        $token = bin2hex(random_bytes(20)); // Genera algo como 'a1b2c3d4...'
        
        //guarda el token en la base de datos
        $sql = "UPDATE usuarios SET token_recuperacion = ? WHERE email = ?";
        $pdo->prepare($sql)->execute([$token, $email]);

        // configurar el email
        $para = $email;
        $titulo = "Recuperar Contraseña - Monkey's";
        $enlace = "https://monkeys.great-site.net/reset_password.php?token=" . $token;
        
        $mensaje_mail = "Hola, haz clic en el siguiente enlace para cambiar tu clave: \n\n" . $enlace;
        
        //cabeceras básicas para que no lo rebote el servidor
        $headers = "From: no-reply@monkeys.great-site.ne" . "\r\n" .
                   "Reply-To: no-reply@monkeys.great-site.ne" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        //enviar usando la función nativa mail()
      if (@mail($para, $titulo, $mensaje_mail, $headers)) {
            $mensaje = "Te hemos enviado un enlace a tu correo.";
        } else {
            //este es el enlace  para "saltar" a la otra página, ya que el servidor gratuito
            //falla al enviar los emails de recuperación.
            $mensaje = "El servidor de correo falló. Usa este enlace de prueba: <br>
                        <a href='$enlace' class='btn btn-warning btn-sm mt-2'>Restablecer ahora mismo</a>";
        }
    } else {
        $mensaje = "Ese correo no está registrado.";
    }
}
?>

<?php require 'layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body p-4 text-center">
                    <img src="logo1.png" width="100" class="mb-3" alt="Logo">
                    
                    <h4 class="fw-bold mb-3">Recuperar Acceso</h4>
                    <p class="text-muted small mb-4">Introduce tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-info small py-2"><?= $mensaje ?></div>
                    <?php endif; ?>

                    <form action="recuperar.php" method="POST">
                        <div class="mb-4 text-start">
                            <label class="form-label small fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control shadow-sm" placeholder="usuario@ejemplo.com" required>
                        </div>
                        
                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">
                            ENVIAR ENLACE
                        </button>
                    </form>
                    
                    <div class="mt-4">
                        <a href="login.php" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Volver al login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>