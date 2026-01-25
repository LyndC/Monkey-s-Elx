*Monkey's - E-commerce Store
Proyecto final para el módulo de Desarrollo Web en Entorno Servidor. 
Una plataforma de comercio electrónico completa desarrollada en PHP y MySQL, centrada en la seguridad, la gestión de roles y la integración de servicios externos.

🔗 Demo en vivo: https://monkeys.great-site.net/
-------------------------------------------------------------------------------------------------------------------------------------------------------------------
*Características Principales
Arquitectura Modular: Uso de layouts reutilizables y carga de clases mediante POO.

Gestión de Roles (RBAC): Diferenciación funcional entre Administrador, Empleado y Cliente.

Ciclo CRUD Completo: Gestión dinámica de artículos, categorías, usuarios y pedidos.

Seguridad Avanzada: * Consultas preparadas con PDO para prevenir SQL Injection.

Hasheo de contraseñas con password_hash().

Validación de DNI mediante algoritmo de Módulo 23 (Frontend y Backend).

Control de búfer de salida con ob_start().

Sistema de Ventas: Carrito de compra persistente en sesión y pasarela de pago simulada con Stripe API.

Reporting: Panel de analítica con ingresos totales y productos más vendidos.
-----------------------------------------------------------------------------------------------------------------------------------------------------------------
*Tecnologías Utilizadas

Backend: PHP 8.2 (POO).

Base de Datos: MySQL (MariaDB).

Frontend: Bootstrap 5, CSS3, JavaScript.

APIs Externas: Stripe (Pagos), Formspree (Contacto).

Herramientas: Composer, Git, FileZilla.
------------------------------------------------------------------------------------------------------------------------------------------------------------------
*Instalación y Despliegue
Clonar el repositorio:

Bash
git clone https://github.com/LyndC/Monkey-s-Elx.git

Configurar la Base de Datos:

Importar el archivo monkeys_db.sql en tu gestor MySQL.

Configurar las credenciales en conectar_db.php.

Instalar dependencias:

Ejecutar composer install para regenerar la carpeta vendor/ (omitida en el repositorio por seguridad).
------------------------------------------------------------------------------------------------------------------------------------------------------------------
*Por razones de seguridad, la carpeta vendor/ y los archivos de configuración con claves de API han sido excluidos del control de versiones mediante .gitignore.
Además cumplimiento de la RGPD, las cuentas de acceso para la evaluación se han incluido exclusivamente en la Memoria Técnica (PDF) 
entregada a través de la plataforma oficial del curso.

<img width="705" height="1459" alt="image" src="https://github.com/user-attachments/assets/34b4285c-e2a0-4a58-9bce-6df54918a01c" />