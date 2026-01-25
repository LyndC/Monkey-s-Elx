<?php 
//clase Articulo

class Articulo {
    //atributos
    private $codigo;
    private $nombre;
    private $descripcion;
    private $categoria;
    private $precio;
    private $stock;
    private $imagen;
    private $descuento;
    private $activo;
    private $estado;
    
    //constructor
    //Se definen valores nulos para facilitar el uso de  fetch_class o fetch_object
   public function __construct(
    $codigo = null, 
    $nombre = null, 
    $descripcion = null, 
    $categoria = null, 
    $precio = null, 
    $stock = null, 
    $imagen = null, 
    $descuento = null, 
    $activo = null,
    $estado = null
) {
    if ($codigo !== null) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->categoria = $categoria;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->imagen = $imagen;
        $this->descuento = $descuento;
        $this->activo = $activo;
        $this->estado = $estado;
    }
}
       
        
    //getters
    public function getCodigo() {
        return $this->codigo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getCategoria() {
        return $this->categoria;
    }
    public function getPrecio() {
        return $this->precio;
    }

     public function getStock() {
        return $this->stock;
    }

    public function getImagen() {
        return $this->imagen;
    }

     public function getDescuento() {
        return $this->descuento;
    }

     public function getActivo() {
        return $this->activo;
    }

     public function getEstado() {
        return $this->estado;
    }

   
        
    //setters
    //al igual que en usuarios el código no llevará setter por seguridad 
    public function setNombre($nombre) { 
        $this->nombre = $nombre; 
    }
    public function setDescripcion($descripcion) { 
        $this->descripcion = $descripcion; 
    }
    public function setCategoria($categoria) { 
        $this->categoria = $categoria; 
    }
    public function setPrecio($precio) { 
        $this->precio = $precio; 
    }
     public function setStock($stock) {
        $this->stock = $stock;
    }
    public function setImagen($imagen) { 
        $this->imagen = $imagen; 
    }
     public function setDescuento($descuento) {
        $this->descuento = $descuento;
    }
     public function setActivo($activo) {
        $this->activo = $activo;
    }
     public function setEstado($estado) {
        $this->estado = $estado;
    }
   

    //métodos
    //devuelve el precio formateado con el símbolo del euro.
    public function getPrecioFormateado() {
        return number_format($this->precio, 2, ',', '.') . " €";
    }

    //muestra resumen del artículo.
    public function mostrarResumen() {
        return "<strong>{$this->nombre}</strong> ({$this->categoria}): " . $this->getPrecioFormateado();
    }

    //verificamos la categoria del artículo
    
    public function esDeCategoria($cat) {
        return strtolower($this->categoria) === strtolower($cat);
    }

    //verificamos stock
    public function hayStock() {
        return $this->stock > 0;
    }
    //método útil para los descuentos que se realizan en tienda
    public function getPrecioRebajado() {
    $rebaja = $this->precio * ($this->descuento / 100);
    $precioFinal = $this->precio - $rebaja;
    return number_format($precioFinal, 2, ',', '.') . " €";
}

}
?>