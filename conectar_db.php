<?php 
// como en todas las aplicaciones web, se empieza por la DB, primero la creamos y luego conectamos con php
//esta vez haremos un if, para que cuando estemos haciendo las pruebas en local entre en el if y cuando lo suba ainfinity entrará en el else
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    //Datos para servidor local XAMPP
    define("HOSTNAME", "localhost");
    define("USER_DB", "root");
    define("PASSWORD", "");
    define("DATABASE", "monkeys");
} else {
    //Datos para InfinityFree 
    define("HOSTNAME", "sql308.infinityfree.com");
    define("USER_DB", "if0_40707191");
    define("PASSWORD", "Lynd281286");
    define("DATABASE", "if0_40707191_monkeys");
}

function conectar(){
    $dsn= "mysql:host=".HOSTNAME.";dbname=".DATABASE.";charset=utf8";

    try {
        $pdo = new PDO($dsn, USER_DB, PASSWORD);
          //configuramos los atributos para mayor seguridad
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
}
}

?>