<?php
//primero definimos la clase
require_once 'clases/Usuario.php';
//miramos si la sesión esta apagada con none, si es asi la encendemos
//y si esta encendida la dejamos así
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
    //aquí no conectamos a la DB, lo hará cada archivo según lo necesitemos
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monkey's Elx - Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --marron-texto: #301a12ff;
            --rosa-principal: #e2b5c4ff; 
            --rosa-fuerte: #e6007e;    /* Para el botón de registro y alertas */
        }

        /* BARRA PRINCIPAL */
        .navbar-main {
            background-color: white;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .logo-escudo {
            height: 75px; 
            width: auto;
        }

        .brand-name {
            font-weight: 800;
            color: var(--marron-texto);
            font-size: 1.4rem;
            letter-spacing: 1px;
        }

        /* BUSCADOR GRANDE */
        .search-wrapper {
            flex: 1;
            max-width: 700px; 
            margin: 0 40px;
        }

        .search-bar {
            border-radius: 30px;
            padding: 12px 25px;
            background-color: #f1f3f4;
            border: 1px solid transparent;
            font-size: 1rem;
        }

        .search-bar:focus {
            background-color: white;
            border-color: var(--rosa-fuerte);
            box-shadow: 0 0 8px rgba(230, 0, 126, 0.2);
        }

        /* ICONOS DERECHA */
        .nav-link-icon {
            color: var(--marron-texto);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .nav-link-icon:hover {
            color: var(--rosa-fuerte);
        }

        .icon-size {
            font-size: 1.5rem;
            display: block;
            margin: 0 auto;
        }
        
        .category-bar {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        .category-link {
            color: #333;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 12px 20px !important;
        }

        .category-link:hover {
            color: var(--rosa-fuerte);
            background-color: var(--rosa-principal);
        }

        .carousel-control-prev-icon,
.carousel-control-next-icon {
  background-color: rgba(0,0,0,0.5); /* fondo oscuro */
  border-radius: 50%;                /* círculo */
  width: 2rem;
  height: 2rem;
}

.card-img-top {
  height: 250px;          
  object-fit: cover;     
}
.parallax-window {
    background-image: url('fondo-parallax.jpg');
    min-height: 400px;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    margin-top: 50px;
    margin-bottom: 50px;
}

@media only screen and (max-width: 768px) {
    .parallax-window {
        background-attachment: scroll;
    }
}
    </style>
</head>
<body>
<!--Navbar-->
<nav class="navbar-main">
    <div class="container d-flex align-items-center">
        
        <a href="index.php" class="d-flex align-items-center text-decoration-none">
            <img src="logo1.png" alt="Monkey's Elx" class="logo-escudo me-2">
            <span class="brand-name d-none d-lg-inline">MONKEY'S</span>
        </a>

        <div class="search-wrapper d-none d-md-block">
            <form action="buscar.php" metod= "GET" class="position-relative">
                <input type="text" name = "codigo" class="form-control search-bar" placeholder="¿Qué estás buscando hoy?">
                <button type="submit" class="btn position-absolute end-0 top-50 translate-middle-y me-3 border-0">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <div class="d-flex align-items-center ms-auto">
    <?php if (isset($_SESSION['usuario'])): ?>
        <div class="dropdown">
            <a href="#" class="nav-link-icon text-center px-3 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-check icon-size"></i>
                <span class="d-none d-sm-inline"><?= $_SESSION['usuario']->getNombre(); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><h6 class="dropdown-header">Hola, <?= $_SESSION['usuario']->getNombre(); ?></h6></li>
                <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-gear me-2"></i>Mi Perfil</a></li>
                
                <?php if ($_SESSION['usuario']->esAdmin()): ?>
                    <li><a class="dropdown-item text-primary" href="admin_panel.php"><i class="bi bi-shield-lock me-2"></i>Panel Admin</a></li>
                <?php endif; ?>
                
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    <?php else: ?>
        <a href="login.php" class="nav-link-icon text-center px-3">
            <i class="bi bi-person icon-size"></i>
            <span class="d-none d-sm-inline">Login</span>
        </a>
        <a href="registro.php" class="btn btn-warning rounded-pill px-4 ms-2 fw-bold d-none d-lg-block">
            Registrarse
        </a>
    <?php endif; ?>

    <a href="carrito.php" class="nav-link-icon text-center px-3 position-relative">
    <i class="bi bi-cart icon-size"></i>
    <?php 
        // Contamos cuántos productos hay en total
        $total_items = 0;
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            // Sumamos todas las cantidades
            foreach ($_SESSION['carrito'] as $cantidad) {
                $total_items += $cantidad;
            }
        }
    ?>
    <span class="badge rounded-pill bg-danger position-absolute top-0 start-50">
        <?php echo $total_items; ?>
    </span>
    <span class="d-none d-sm-inline">Carrito</span>
</a>
</div>
    </div>
</nav>