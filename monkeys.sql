-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-01-2026 a las 01:00:11
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
('ART001', 'Smart TV Hisense 55\"', 'Resolución 4K UHD con HDR10 y Smart TV.', 1, 450, 10, 'img/tvhisense.jpg', 0, 1, 'nuevo'),
('ART002', 'PlayStation 5 Slim', 'Consola con lector de discos y 1TB SSD.', 2, 549.99, 5, 'img/PS5.jpg', 0, 1, 'nuevo'),
('ART003', 'Portátil HP EliteBook', 'Equipo revisado con garantía de 1 año.', 1, 299, 3, 'img/portatiles.jpg', 10, 1, 'reutilizado');

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
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`dni`, `clave`, `nombre`, `apellidos`, `direccion`, `localidad`, `provincia`, `telefono`, `email`, `rol`, `fecha_registro`, `activo`) VALUES
('21765048X', '$2y$10$AYCUivvHUMm79tZS66ljOeJaA8/j5/SC2S44FYAWZRNd6VLEXbvJW', 'Victor Manuel', 'Sánchez Tirado', 'calle Trinxant, 134', 'Barcelona', 'Barcelona', '622090109', 'victorm@hotmail.com', 'empleado', '2026-01-07 21:09:24', 1),
('51236755T', '$2y$10$SUCOpxxn1p3IDg/RMVElkeaHYiKj1lAbGBpmmRQMa8iwwgZnzClBq', 'Lynd Cristina', 'Sánchez Laos', 'Calle Reina Victoria, 108', 'Elche', 'Alicante', '678401165', 'lynd1785@hotmail.com', 'admin', '2026-01-07 21:06:58', 1),
('60887342V', '$2y$10$xsoChLN5KkU/U6G/vl/EXeFOjyCIpWL0v2TndMThSu.Lvx8j1SBO2', 'Geny', 'Gutierrez', 'Calle Cristobal Sanz, 70 - Local', 'Elche', 'Alicante', '678401165', 'geny_ggm@hotmail.com', 'cliente', '2026-01-07 21:07:59', 1);

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
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT;

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
