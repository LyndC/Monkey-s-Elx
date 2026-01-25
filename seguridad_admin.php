<?php
//ya que estamos trabajando con objetos, primero cargamos la clase 
require_once 'clases/Usuario.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//si no existe el usuario en la sesión O no tiene el método esAdmin o no lo es...
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']->esAdmin()) {
    //lo mandamos al login con un mensaje de error
    header("Location: login.php?error=acceso_restringido");
    exit();
}
// Si pasa de aquí, el usuario es Administrador y puede ver la página
//este codigo lo reutilizaremos en archivos especiales para admin