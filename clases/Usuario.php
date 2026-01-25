<?php
//clase usuario
class Usuario {
    //atributos
    private $dni;
    private $clave;
    private $nombre;
    private $apellidos;
    private $direccion;
    private $localidad;
    private $provincia;
    private $telefono;
    private $email;
    private $rol;
    private $fecha_registro;
    private $activo;
    private $token_recuperacion;

    //constructor
    //Se definen valore nulos para facilitar el uso de  fetch_class o fetch_object
    public function __construct(
        $dni = null,
        $clave = null,
        $nombre = null, 
        $apellidos = null,
        $direccion = null,
        $localidad = null,
        $provincia = null,
        $telefono = null,
        $email = null,
        $rol = null,
        $fecha_registro = null,
        $activo = null,
        $token_recuperacion = null
        ) {
            if ($dni !== null) {
        $this->dni = $dni;
        $this->clave =$clave;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->direccion = $direccion;
        $this->localidad = $localidad;
        $this->provincia = $provincia;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->rol = $rol;    
        $this->fecha_registro = $fecha_registro;
        $this ->activo = $activo;   
        $this ->token_recuperacion = $token_recuperacion;  
            }
    }
    //getters
    public function getDni() {
        return $this->dni;
    }

     public function getClave() {
        return $this->clave;
    }

    public function getNombre() {
        return $this->nombre;
    }

     public function getApellidos() {
        return $this->apellidos;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getLocalidad() {
        return $this->localidad;
    }

    public function getProvincia() {
        return $this->provincia;
    }

    public function getTelefono() {
        return $this->telefono;
    }

     public function getEmail() {
        return $this->email;
    }
    
    public function getRol() {
        return $this->rol;
    }

     public function getFecha_registro() {
        return $this->fecha_registro;
    }

     public function getActivo() {
        return $this->activo;
    }

    public function getTokenRecuperacion() {
    return $this->token_recuperacion;
}
    

    //setters
    //por seguridad no ponemos dni; ya que estos datos deberian ser inmutables y no se modificará
    
    public function setClave($clave) {
        return $this->clave = $clave;
    }

    public function setNombre($nombre) {
        return
    $this->nombre = $nombre;
    }

    public function setApellidos($apellidos) {
        return $this->apellidos = $apellidos;
    }

    public function setDireccion($direccion) {
        return $this->direccion = $direccion;
    }

    public function setLocalidad($localidad) {
        return $this->localidad = $localidad;
    }

    public function setProvincia($provincia) {
        return $this->provincia = $provincia;
    }

    public function setTelefono($telefono) {
        return $this->telefono = $telefono;
    }

     public function setEmail($email) {
        return $this->email = $email;
    }

    public function setActivo($activo){
        return $this->activo =$activo;
    }

    public function setTokenRecuperacion($token) {
    $this->token_recuperacion = $token; 
    }
     
    //métodos
    public function mostrarInfo() {
        return "{$this->nombre} ({$this->dni}) - Rol: {$this->rol}";
    }

    public function esAdmin() {
        return $this->rol === 'admin';
    }

    public function esEmpleado() {
        return $this->rol === 'empleado';
    }

    public function esCliente() {
        return $this->rol === 'cliente';
    }

    public function puedeGestionarArticulos() {
    return $this->rol === 'administrador'; //si el rol es administrador puede gestionar los artículos
    }

    public function puedeVerArticulos() {
    return true; // todos los roles pueden ver los artículos
    }

    public function puedeBuscarYOrdenar() { //todos los roles pueden buscar y ordenar artículos
    return true;
    }   

    public function puedeCambiarVista() {
    return true; // todos los roles pueden cambiar entre vista de usuarios y artículos
    }
}
?>