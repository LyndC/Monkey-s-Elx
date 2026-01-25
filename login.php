<?php 
require_once 'clases/Usuario.php';
// Evitamos el error de sesión duplicada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conectar_db.php'; 


$error = ""; 

// SOLO procesamos si se ha enviado el formulario por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['dni'], $_POST['password'])) {
        try {
            $pdo = conectar();
            //buscamos el ususario y lo convertimos en objeto
            $sql = "SELECT * FROM usuarios WHERE dni = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['dni']]);
            //mapeamos las columnas de DB a los atributos de la clase Usuario
            $stmt ->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($_POST['password'], $usuario->getClave())) {
                //guardamos el objeto en la sesión
                $_SESSION['usuario'] = $usuario;
                
                if ($usuario->esAdmin()) {
                    header("Location: admin_panel.php");
                } elseif ($usuario -> esEmpleado()){
                    header("Location: empleado_panel.php");
                } else {
                    header("Location: cliente_panel.php");
                }
                exit;
            } else {
                $error = "Datos incorrectos.";
            }
        } catch (PDOException $e) {
            $error = "Error de conexión" . $e->getMessage();
        }
    }
}
require 'layouts/header.php';

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body p-4 text-center">
                    <img src="logo1.png" width="100" class="mb-3" alt="Logo">
                    <h4 class="fw-bold mb-3">Acceso Monkey's</h4>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger small py-2"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-bold">DNI</label>
                            <input type="text" name="dni" class="form-control shadow-sm" placeholder="12345678X" required>
                        </div>
                        <div class="mb-4 text-start">
                        <label class="form-label small fw-bold">Contraseña</label>
                        <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control shadow-sm" placeholder="••••••••" required style="border-right: none;">
                        <button class="btn btn-outline-secondary shadow-sm" type="button" id="togglePassword" style="border-left: none; background: white;">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">
                            ENTRAR
                        </button>
                    </form>
                    <div class="text-end mt-2">
                    <a href="recuperar.php" class="text-muted small">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Volver a la tienda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //código JS para el ojo de la contraseña
    //capturamos los elementos del HTML usando sus IDs
    //el botón que el usuario pulsará
    const togglePassword = document.querySelector('#togglePassword');
    //el campo de texto donde se escribe la clave
    const password = document.querySelector('#password');
    //el icono que está dentro del botón( para cambiar al icono del ojo)
    const eyeIcon = document.querySelector('#eyeIcon');
    //escuchamos el evenoc click en el botón del ojo
    togglePassword.addEventListener('click', function () {
        // Cambiamos entre tipo password y tipo text, es decir que si pulsamos el ojo, se ve texto (la contraseña)
        //y si no lo volvemos a poner en password (se oculta)
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        //aplicamos el nuevo input
        password.setAttribute('type', type);
        
        //cambiamos el icono del ojo
        if (type === 'password') {
            //si está oculto, ponemos el ojo normal
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        } else {
            //si se está viendo, ponemos el ojo tachado
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        }
    });
</script>

<?php 
// cargamos el footer
require 'layouts/footer.php'; 
?>