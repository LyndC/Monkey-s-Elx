-- 1. Categorías 
CREATE TABLE categorias (
    codigo INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(200),
    imagen VARCHAR(200),
    activo TINYINT(1) DEFAULT 1 -- tipo de dato numerico muy pequeño 1 byte (activo/inactivo) baja lógica
);

-- 2. Artículos
CREATE TABLE articulos (
    codigo VARCHAR(8) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(200),
    categoria INT(11),
    precio FLOAT NOT NULL,
    stock INT(11) DEFAULT 1,
    imagen VARCHAR(200),
    descuento FLOAT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1, --(baja lógica)
    FOREIGN KEY (categoria) REFERENCES categorias(codigo)
);

-- 3. Usuarios
CREATE TABLE usuarios (
    dni VARCHAR(9) PRIMARY KEY,
    clave VARCHAR(60) NOT NULL,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR(75),
    direccion VARCHAR(50),
    localidad VARCHAR(30),
    provincia VARCHAR(30),
    telefono VARCHAR(9),
    email VARCHAR(30) UNIQUE,
    rol VARCHAR(20) DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1 -- baja lógica ( activo/inactivo)
);

ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('admin', 'empleado', 'cliente') DEFAULT 'cliente';


-- 4. Pedidos (De momento no se hacen envíos, sólo recogida en tienda)
CREATE TABLE pedidos (
    idPedido INT(11) AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total FLOAT NOT NULL,
    metodo_pago VARCHAR(20), 
    estado SMALLINT(6) DEFAULT 0, -- similar a tinyint, pero con más capacidad 2 bytes para varios estados
    codUsuario VARCHAR(9),
    codigo_recogida VARCHAR(10) UNIQUE,
    fecha_recogida_real DATETIME NULL,
    activo TINYINT(1) DEFAULT 1, 
    FOREIGN KEY (codUsuario) REFERENCES usuarios(dni)
);

-- 5. Detalle del Pedido
CREATE TABLE lineapedido (
    numPedido INT(11),
    numLinea INT(11),
    codArticulo VARCHAR(8),
    cantidad INT(11),
    precio_unitario FLOAT,
    descuento_aplicado FLOAT DEFAULT 0,
    PRIMARY KEY (numPedido, numLinea),
    FOREIGN KEY (numPedido) REFERENCES pedidos(idPedido),
    FOREIGN KEY (codArticulo) REFERENCES articulos(codigo)
);

-- 6. Carrito Persistente
CREATE TABLE carrito (
    idCarrito INT(11) AUTO_INCREMENT PRIMARY KEY,
    codUsuario VARCHAR(9) NOT NULL,
    codArticulo VARCHAR(8) NOT NULL,
    cantidad INT(11) DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (codUsuario) REFERENCES usuarios(dni),
    FOREIGN KEY (codArticulo) REFERENCES articulos(codigo)
);