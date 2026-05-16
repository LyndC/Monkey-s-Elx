<?php
require_once 'clases/Usuario.php'; 
require_once 'conectar_db.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//Funcion para validacion de DNI
function validarDniMatematico($dni) {
    //quitamos los espacios y pasamos a Mayúsculas
    $dni = strtoupper(trim($dni));
    //formato básico: 8 números y 1 letra válida -regex
    if (!preg_match('/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $dni)) {
        return false;//si retorna falso(no es correcto) no seguimos
    }
    //lógica matematica para la validacion del dni
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE"; //declaramos las letras
    $numero = substr($dni, 0, 8);//extraemos los numeros (primeros 8 caracteres)
    $letraEnviada = substr($dni, -1); //extraemos la letra (último caracter)
    $letraCorrecta = $letras[$numero % 23];//modulo 23 (el resto de dividir el num entre 23 nos da la posicion de la letra)
    return ($letraEnviada === $letraCorrecta);//comparamos si la letra enviada es la letra correcta
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $dni       = strtoupper(trim($_POST['dni'] ?? '')); // forzamos mayúsculas y borramos espacios
    $nombre    = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $localidad = trim($_POST['localidad'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');

    if (empty($email) || empty($password) || empty($dni)) {
        $error = "El DNI introducido no es válido (la letra no corresponde al número).";    
    } else if (!validarDniMatematico($dni)) {
    $error = "El DNI introducido no es válido (la letra no corresponde al número).";
    } else {
        try {
            $pdo = conectar();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmtCheck = $pdo->prepare("SELECT dni FROM usuarios WHERE email = ? OR dni = ?");
            $stmtCheck->execute([$email, $dni]);
            
            if ($stmtCheck->fetch()) {
                $error = "El DNI o el Email ya están registrados.";
            } else {
                $sqlUser = "INSERT INTO usuarios (dni, clave, nombre, apellidos, direccion, localidad, provincia, telefono, email, rol, activo) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmtUser = $pdo->prepare($sqlUser);
                $stmtUser->execute([
                    $dni, $passwordHash, $nombre, $apellidos, $direccion, 
                    $localidad, $provincia, $telefono, $email, 'cliente', 1
                ]);
                //creamos objeto para la sesión
                $usuarioParaSesion = new Usuario($dni, $passwordHash, $nombre, null, $direccion, null, null, $telefono, $email, 'cliente', date('Y-m-d'), 1);
                $_SESSION['usuario'] = $usuarioParaSesion;

                header("Location: cliente_panel.php");
                exit;
            } 
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        } 
    } 
} 

require_once 'layouts/header.php'; 
?>



<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
            <div class="card-body p-4 text-center">
                <img src="logo1.png" width="100" class="mb-3" alt="Logo">
                <h4 class="fw-bold mb-3">Crear Cuenta Monkey's</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger small py-2 mb-4"><?= $error ?></div>
                <?php endif; ?>

                <form action="registro.php" method="POST" class="row g-3 text-start">
                    <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">DNI</label>
                    <input type="text" name="dni" class="form-control" placeholder="12345678X" required>
                    <div class="invalid-feedback">
                     DNI incorrecto.
                    </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Nombre </label>
                        <input type="text" name="nombre" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Apellidos </label>
                        <input type="text" name="apellidos" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Dirección </label>
                        <input type="text" name="direccion" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Localidad </label>
                        <input type="text" name="localidad" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Provincia </label>
                        <input type="text" name="provincia" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="tel" 
                        name="telefono" 
                        class="form-control shadow-sm" 
                        pattern="[0-9]{9,15}" 
                        maxlength="15" 
                        required 
                        title="El teléfono debe contener solo números (entre 9 y 15 dígitos, sin letras ni espacios)">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control shadow-sm" required>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">
                            REGISTRARME
                        </button>
                    </div>
                </form>
                
                <div class="mt-4">
                    <p class="small text-muted mb-1">¿Ya tienes cuenta?</p>
                    <a href="login.php" class="text-dark fw-bold text-decoration-none small">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>
                <!--Usamos javaScript para una doble capa de seguridad-->
<script>
const inputDni = document.querySelector('input[name="dni"]');

inputDni.addEventListener('blur', function() {
    const dniVal = this.value.toUpperCase().trim();
    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    const regex = /^[0-9]{8}[A-Z]$/;
    
    
    this.classList.remove('is-invalid', 'is-valid');

    if (dniVal !== "") {
        let esValido = false;

        //primero miramos si tiene el formato (8 números y 1 letra)
        if (regex.test(dniVal)) {
            const numero = dniVal.substring(0, 8);
            const letraEnviada = dniVal.substring(8, 9);
            const letraCorrecta = letras[numero % 23];
            
            //comprobamos si la letra es la que toca por matemáticas
            if (letraEnviada === letraCorrecta) {
                esValido = true;
            }
        }

        //si no es válido, se pondra en rojo
        if (esValido) {
            this.classList.add('is-valid');
        } else {
            this.classList.add('is-invalid');
             this.value = ""; 
        }
    }
});
</script>
<?php 
require_once 'layouts/footer.php'; 
?>