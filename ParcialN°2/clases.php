<?php

interface Prestable {
    public function obtenerDetallesPrestamo(): string;

}
abstract class RecursoBiblioteca implements Prestable {
    protected $id;
    protected $titulo;
    protected $autor;
    protected $anio;
    protected $estado;

    public function __construct($id, $titulo, $autor, $anio, $estado) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->anio = $anio;
        $this->estado = $estado;

    public function __construct($datos) {
        foreach ($datos as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

    }

    }
    abstract public function obtenerDetallesPrestamo(): string;


// Implementar las clases Libro, Revista y DVD aquí

class Libro extends RecursoBiblioteca implements Prestable {
    private $isbn;

    public function __construct($id, $titulo, $autor, $anio, $estado, $isbn) {
        parent::__construct($id, $titulo, $autor, $anio, $estado);
        $this->isbn = $isbn;
    }

    public function obtenerDetallesPrestamo(): string {
        return "Libro ISBN: {$this->isbn}";
    }
}

class Revista extends RecursoBiblioteca implements Prestable {
    private $numeroEdicion;

    public function __construct($id, $titulo, $autor, $anio, $estado, $numeroEdicion) {
        parent::__construct($id, $titulo, $autor, $anio, $estado);
        $this->numeroEdicion = $numeroEdicion;
    }

    public function obtenerDetallesPrestamo(): string {
        return "Revista N° Edición: {$this->numeroEdicion}";
    }
}

class DVD extends RecursoBiblioteca implements Prestable {
    private $duracion;

    public function __construct($id, $titulo, $autor, $anio, $estado, $duracion) {
        parent::__construct($id, $titulo, $autor, $anio, $estado);
        $this->duracion = $duracion;
    }

    public function obtenerDetallesPrestamo(): string {
        return "DVD Duración: {$this->duracion} minutos";
    }
}





class GestorBiblioteca {
    private $recursos = [];


    public function cargarRecursos($archivoJSON) {
        $datos = json_decode(file_get_contents('biblioteca.json'),true);
        foreach ($datos as $recurso){
            $this->agregarRecursoDesdeArray($recurso);
        }
    }


    private function agregarRecursoDesdeArray($recurso) {
        switch ($recurso['tipo']) {
            case 'libro':
                $nuevoRecurso = new Libro($recurso['id'], $recurso['titulo'], $recurso['autor'], $recurso['anio'], $recurso['estado'], $recurso['isbn']);
                break;
            case 'revista':
                $nuevoRecurso = new Revista($recurso['id'], $recurso['titulo'], $recurso['autor'], $recurso['anio'], $recurso['estado'], $recurso['numeroEdicion']);
                break;
            case 'dvd':
                $nuevoRecurso = new DVD($recurso['id'], $recurso['titulo'], $recurso['autor'], $recurso['anio'], $recurso['estado'], $recurso['duracion']);
                break;
            default:
                return;
        }
        $this->agregarRecurso($nuevoRecurso);
    }

    public function agregarRecurso(RecursoBiblioteca $recurso) {
        $this->rescursos[] = $recurso;

    }

    public function eliminarRecurso($id) {
        foreach ($this-> recursos as $key => $recurso) {
            if($recurso ->id === $id){
                inset($this-> recursos[$key]);
                break;
            }
        
        }
    }

    public function actualizarRecurso(RecursoBiblioteca $recurso){
        foreach ($this-> recurso as $key){
            if ($recurso->id === $id){
                $recurso->estado = $nuevoEstado;
                break;
            }
        }
    }

    public function buscarRecursosPorEstado($estado) {
        return array_filter($this->recursos, function ($recurso) use ($estado) {
            return $recurso->estado === $estado;
        });
    }

    public function listarRecursos($filtroEstado = '', $campoOrden = 'id', $direccionOrden = 'ASC') {
        $recursos = $filtroEstado ? $this->buscarRecursosPorEstado($filtroEstado) : $this->recursos;
        usort($recursos, function ($a, $b) use ($campoOrden, $direccionOrden) {
            if ($a->$campoOrden === $b->$campoOrden) {
                return 0;
            }
            return ($direccionOrden === 'ASC' ? $a->$campoOrden < $b->$campoOrden : $a->$campoOrden > $b->$campoOrden) ? -1 : 1;
        });
        return $recursos;
    }

    //cambiar en guardarRecursos al nombre del archivo por si falla 
    public function guardarRecursos($archivoJSON) {
        $datos = array_map(function ($recurso) {
            return [
                'id' => $recurso->id,
                'titulo' => $recurso->titulo,
                'autor' => $recurso->autor,
                'anio' => $recurso->anio,
                'estado' => $recurso->estado,
                'tipo' => strtolower((new ReflectionClass($recurso))->getShortName())
            ];
        }, $this->recursos);
        file_put_contents('biblioteca.json', json_encode($datos));
    }

}


// Implementar los demás métodos aquí
$estadosLegibles = [
    'disponible' => 'DISPONIBLE',
    'prestado' => 'PRESTADO',
    'en_reparacion' => 'EN REPARACIÓN'
];





