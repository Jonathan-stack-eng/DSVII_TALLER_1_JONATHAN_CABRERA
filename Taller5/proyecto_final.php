<?php

// Clase Estudiante
class Estudiante {
    private int $id;
    private string $nombre;
    private int $edad;
    private string $carrera;
    private array $materias; // Arreglo asociativo: ['materia' => calificación]

    // Constructor
    public function __construct(int $id, string $nombre, int $edad, string $carrera) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->carrera = $carrera;
        $this->materias = [];
    }

    // Agregar una materia y su calificación
    public function agregarMateria(string $materia, float $calificacion): void {
        $this->materias[$materia] = $calificacion;
    }

    // Obtener el promedio de las calificaciones
    public function obtenerPromedio(): float {
        if (count($this->materias) === 0) {
            return 0;
        }
        return array_sum($this->materias) / count($this->materias);
    }

    // Obtener los detalles del estudiante como un arreglo asociativo
    public function obtenerDetalles(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'edad' => $this->edad,
            'carrera' => $this->carrera,
            'materias' => $this->materias,
            'promedio' => $this->obtenerPromedio()
        ];
    }

    // Método __toString para imprimir los detalles del estudiante
    public function __toString(): string {
        $detalles = $this->obtenerDetalles();
        return "ID: {$detalles['id']}, Nombre: {$detalles['nombre']}, Carrera: {$detalles['carrera']}, Promedio: {$detalles['promedio']}\n";
    }

    // Obtener el ID del estudiante
    public function getId(): int {
        return $this->id;
    }

    // Obtener el nombre del estudiante
    public function getNombre(): string {
        return $this->nombre;
    }

    // Obtener la carrera del estudiante
    public function getCarrera(): string {
        return $this->carrera;
    }
}

// Clase SistemaGestionEstudiantes
class SistemaGestionEstudiantes {
    private array $estudiantes;
    private array $graduados;

    // Constructor
    public function __construct() {
        $this->estudiantes = [];
        $this->graduados = [];
    }

    // Agregar un nuevo estudiante
    public function agregarEstudiante(Estudiante $estudiante): void {
        $this->estudiantes[$estudiante->getId()] = $estudiante;
    }

    // Obtener un estudiante por su ID
    public function obtenerEstudiante(int $id): ?Estudiante {
        return $this->estudiantes[$id] ?? null;
    }

    // Listar todos los estudiantes
    public function listarEstudiantes(): array {
        return $this->estudiantes;
    }

    // Calcular el promedio general de todos los estudiantes
    public function calcularPromedioGeneral(): float {
        $totalPromedio = array_map(fn($estudiante) => $estudiante->obtenerPromedio(), $this->estudiantes);
        return array_sum($totalPromedio) / count($totalPromedio);
    }

    // Obtener estudiantes por carrera
    public function obtenerEstudiantesPorCarrera(string $carrera): array {
        return array_filter($this->estudiantes, fn($estudiante) => $estudiante->getCarrera() === $carrera);
    }

    // Obtener el mejor estudiante
    public function obtenerMejorEstudiante(): ?Estudiante {
        return array_reduce($this->estudiantes, fn($mejor, $estudiante) => 
            $mejor === null || $estudiante->obtenerPromedio() > $mejor->obtenerPromedio() ? $estudiante : $mejor
        );
    }

    // Generar reporte de rendimiento
    public function generarReporteRendimiento(): array {
        $reporte = [];
        foreach ($this->estudiantes as $estudiante) {
            foreach ($estudiante->obtenerDetalles()['materias'] as $materia => $calificacion) {
                if (!isset($reporte[$materia])) {
                    $reporte[$materia] = ['suma' => 0, 'conteo' => 0, 'max' => $calificacion, 'min' => $calificacion];
                }
                $reporte[$materia]['suma'] += $calificacion;
                $reporte[$materia]['conteo']++;
                $reporte[$materia]['max'] = max($reporte[$materia]['max'], $calificacion);
                $reporte[$materia]['min'] = min($reporte[$materia]['min'], $calificacion);
            }
        }

        foreach ($reporte as $materia => &$datos) {
            $datos['promedio'] = $datos['suma'] / $datos['conteo'];
        }
        return $reporte;
    }

    // Graduar a un estudiante
    public function graduarEstudiante(int $id): void {
        if (isset($this->estudiantes[$id])) {
            $this->graduados[$id] = $this->estudiantes[$id];
            unset($this->estudiantes[$id]);
        }
    }

    // Generar un ranking de estudiantes por promedio
    public function generarRanking(): array {
        uasort($this->estudiantes, fn($a, $b) => $b->obtenerPromedio() <=> $a->obtenerPromedio());
        return $this->estudiantes;
    }
}

// Sección de prueba

// Instanciación del sistema
$sistema = new SistemaGestionEstudiantes();

// Crear estudiantes y agregar materias
$estudiante1 = new Estudiante(1, 'Ana Pérez', 20, 'Ingeniería');
$estudiante1->agregarMateria('Matemáticas', 85);
$estudiante1->agregarMateria('Física', 90);
$sistema->agregarEstudiante($estudiante1);

$estudiante2 = new Estudiante(2, 'Luis Gómez', 22, 'Medicina');
$estudiante2->agregarMateria('Anatomía', 88);
$estudiante2->agregarMateria('Bioquímica', 92);
$sistema->agregarEstudiante($estudiante2);

// Listar estudiantes
echo "Listado de estudiantes:\n";
foreach ($sistema->listarEstudiantes() as $estudiante) {
    echo $estudiante;
}

// Calcular promedio general
echo "\nPromedio general del sistema: " . $sistema->calcularPromedioGeneral() . "\n";

// Obtener el mejor estudiante
echo "\nMejor estudiante: " . $sistema->obtenerMejorEstudiante() . "\n";

// Generar reporte de rendimiento
echo "\nReporte de rendimiento:\n";
print_r($sistema->generarReporteRendimiento());

?>
