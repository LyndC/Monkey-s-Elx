-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-01-2026 a las 00:00:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `monkeys`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `codigo` varchar(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `categoria` int(11) DEFAULT NULL,
  `precio` float NOT NULL,
  `stock` int(11) DEFAULT 1,
  `imagen` varchar(200) DEFAULT NULL,
  `descuento` float DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `estado` enum('nuevo','reutilizado') DEFAULT 'nuevo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `stock`, `imagen`, `descuento`, `activo`, `estado`) VALUES
('ART0004', 'Televisor Hisense 32\'\'', 'Hisense 32A4Q - HD Smart TV 32 Pulgadas, Modo Juego, Deportes IA, Sonido Dolby DTS HD, Alto Contraste, Función Compartir en el Televisor, DVB - T2, Auto Ordenación de los Canales TDT [Clase de eficien', 1, 199, 6, 'img/1767958291_TelevisorHisense32.jpg', 40, 1, 'reutilizado'),
('ART0005', 'Smart TV METZ', 'METZ TV 40 Pulgadas Smart TV, 40MQF7000Z, QLED, Smart TV 40 Pulgadas, HDR10, Dolby Audio, Bluetooth 5.1, Negro (DVB-T/T2/S/S2/C ATV, Ci+) 2025 [Clase de eficiencia energética E]', 1, 279.99, 7, 'img/1768074777_SmartTVMETZ.jpg', 36, 1, 'nuevo'),
('ART0006', 'PlayStation 4 Slim', 'Sony PlayStation 4 Slim 1TB + 2 Dualshock 4 V2 Negro 1000 GB Wifi - Videoconsolas (PlayStation 4, Negro, 8192 MB, GDDR5, AMD Jaguar, AMD Radeon)', 2, 279, 4, 'img/1768075099_PlayStation4Slim.JPG', 40, 1, 'reutilizado'),
('ART001', 'Smart TV Hisense 55\"', 'Resolución 4K UHD con HDR10 y Smart TV.', 1, 450, 10, 'img/tvhisense.jpg', 35, 1, 'nuevo'),
('ART002', 'PlayStation 5 Slim', 'Consola con lector de discos y 1TB SSD.', 2, 549.99, 6, 'img/PS5.jpg', 30, 1, 'nuevo'),
('ART003', 'Portátil HP EliteBook', 'Equipo revisado con garantía de 1 año.', 1, 250, 7, 'img/portatiles.jpg', 1, 1, 'nuevo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `idCarrito` int(11) NOT NULL,
  `codUsuario` varchar(9) NOT NULL,
  `codArticulo` varchar(8) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `imagen` varchar(200) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`codigo`, `nombre`, `descripcion`, `imagen`, `activo`) VALUES
(1, 'Electrónica', NULL, NULL, 1),
(2, 'Ocio', NULL, NULL, 1),
(3, 'Moda y Accesorios', NULL, NULL, 1),
(4, 'Deporte', NULL, NULL, 1),
(5, 'Música e Instrumentos Musicales', NULL, NULL, 1),
(6, 'Electrodomésticos', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineapedido`
--

CREATE TABLE `lineapedido` (
  `numPedido` int(11) NOT NULL,
  `numLinea` int(11) NOT NULL,
  `codArticulo` varchar(8) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` float DEFAULT NULL,
  `descuento_aplicado` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lineapedido`
--

INSERT INTO `lineapedido` (`numPedido`, `numLinea`, `codArticulo`, `cantidad`, `precio_unitario`, `descuento_aplicado`) VALUES
(2, 1, 'ART003', 1, 299, 0),
(3, 1, 'ART001', 1, 450, 0),
(4, 1, 'ART001', 1, 450, 0),
(5, 1, 'ART002', 1, 549.99, 0),
(6, 1, 'ART001', 1, 450, 0),
(7, 1, 'ART002', 1, 549.99, 0),
(8, 1, 'ART0005', 1, 279.99, 0),
(9, 1, 'ART0004', 1, 199, NULL),
(10, 1, 'ART0005', 1, 279.99, NULL),
(11, 1, 'ART003', 1, 250, 2.5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `idPedido` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` float NOT NULL,
  `metodo_pago` varchar(20) DEFAULT NULL,
  `estado` smallint(6) DEFAULT 0,
  `codUsuario` varchar(9) DEFAULT NULL,
  `codigo_recogida` varchar(10) DEFAULT NULL,
  `fecha_recogida_real` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`idPedido`, `fecha`, `total`, `metodo_pago`, `estado`, `codUsuario`, `codigo_recogida`, `fecha_recogida_real`, `activo`) VALUES
(2, '2026-01-08 02:32:33', 299, 'tienda', 0, '60887342V', 'A2B7C0', '2026-01-08 11:33:51', 1),
(3, '2026-01-08 02:44:57', 450, 'tienda', 3, '60887342V', '371FB9', '2026-01-08 12:04:30', 1),
(4, '2026-01-08 02:45:39', 450, 'tienda', 0, '60887342V', 'C8ED19', NULL, 1),
(5, '2026-01-08 02:46:48', 549.99, 'tienda', 1, '60887342V', '697516', NULL, 1),
(6, '2026-01-08 02:49:22', 450, 'tienda', 1, '60887342V', '890C03', NULL, 1),
(7, '2026-01-08 02:49:38', 549.99, 'tienda', 3, '60887342V', '04275D', NULL, 1),
(8, '2026-01-10 22:28:57', 279.99, 'tienda', 2, '51236755T', '75A61B', NULL, 1),
(9, '2026-01-12 01:57:25', 119.4, 'tienda', 0, '60887342V', '66BFDD', NULL, 1),
(10, '2026-01-12 01:58:02', 179.194, 'tienda', 0, '60887342V', '3CA81B', NULL, 1),
(11, '2026-01-12 02:03:29', 247.5, 'tienda', 0, '60887342V', '596EE1', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `dni` varchar(9) NOT NULL,
  `clave` varchar(60) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellidos` varchar(75) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `localidad` varchar(30) DEFAULT NULL,
  `provincia` varchar(30) DEFAULT NULL,
  `telefono` varchar(9) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `rol` enum('admin','empleado','cliente') DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  `token_recuperacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `categoria` (`categoria`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`idCarrito`),
  ADD KEY `codUsuario` (`codUsuario`),
  ADD KEY `codArticulo` (`codArticulo`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  ADD PRIMARY KEY (`numPedido`,`numLinea`),
  ADD KEY `codArticulo` (`codArticulo`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idPedido`),
  ADD UNIQUE KEY `codigo_recogida` (`codigo_recogida`),
  ADD KEY `codUsuario` (`codUsuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`dni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `idCarrito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`codigo`);

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`codUsuario`) REFERENCES `usuarios` (`dni`),
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`codArticulo`) REFERENCES `articulos` (`codigo`);

--
-- Filtros para la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  ADD CONSTRAINT `lineapedido_ibfk_1` FOREIGN KEY (`numPedido`) REFERENCES `pedidos` (`idPedido`),
  ADD CONSTRAINT `lineapedido_ibfk_2` FOREIGN KEY (`codArticulo`) REFERENCES `articulos` (`codigo`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`codUsuario`) REFERENCES `usuarios` (`dni`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
