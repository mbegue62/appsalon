<?php

namespace Model;

class servicio extends ActiveRecord {
    //objeto
    protected static $tabla = 'servicios';
    protected static $columnasDB = ['id', 'nombre', 'precio'];

    //atributos
    public $id;
    public $nombre;
    public $precio;

    // constructor
    public function __construct($args = []) 
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }

    public function validar(){
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del servicio es Obligatorio';
        }
        if(!$this->precio) {
            self::$alertas['error'][] = 'El precio del servicio es Obligatorio';
        }
        
        
        
        return self::$alertas;
    }


}