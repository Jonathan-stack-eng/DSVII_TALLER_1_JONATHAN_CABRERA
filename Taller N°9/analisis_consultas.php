<?php
require_once "config_pdo.php";

function analizarConsulta($pdo, $sql) {
    try {
        // Ejecutar EXPLAIN
        $stmt = $pdo->prepare("EXPLAIN FORMAT=JSON " . $sql);
        $stmt->execute();
        $explain = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<h3>Análisis de la consulta:</h3>";
        echo "<pre>" . print_r(json_decode($explain['EXPLAIN'], true), true) . "</pre>";

        // Ejecutar la consulta y medir el tiempo
        $inicio = microtime(true);
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $fin = microtime(true);

        echo "Tiempo de ejecución: " . number_format($fin - $inicio, 4) . " segundos<br>";
        echo "Filas afectadas: " . $stmt->rowCount() . "<br>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Ejemplos de consultas para analizar
$consultas = [
    "Búsqueda por categoría" => "
        SELECT p.* 
        FROM productos p 
        WHERE p.categoria_id = 1
    ",
    "Búsqueda por rango de precios" => "
        SELECT p.* 
        FROM productos p 
        WHERE p.precio BETWEEN 100 AND 500
    ",
    "Ventas por período" => "
        SELECT v.*, c.nombre 
        FROM ventas v 
        JOIN clientes c ON v.cliente_id = c.id 
        WHERE v.fecha_venta BETWEEN '2023-01-01' AND '2023-12-31'
    ",
    "Búsqueda de texto completo" => "
        SELECT * 
        FROM productos 
        WHERE MATCH(nombre, descripcion) AGAINST('laptop' IN NATURAL LANGUAGE MODE)
    "
];

foreach ($consultas as $descripcion => $sql) {
    echo "<h2>$descripcion</h2>";
    analizarConsulta($pdo, $sql);
}

$pdo = null;
?>



<?php
require_once "config_pdo.php";

class TransactionManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function setIsolationLevel($level) {
        $levels = [
            'READ UNCOMMITTED',
            'READ COMMITTED',
            'REPEATABLE READ',
            'SERIALIZABLE'
        ];
        
        if (in_array($level, $levels)) {
            $this->pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL " . $level);
            echo "Nivel de aislamiento establecido a: $level<br>";
        }
    }
    
    // Ejemplo de transacción con lectura sucia (READ UNCOMMITTED)
    public function demonstrateDirtyRead() {
        try {
            $this->setIsolationLevel('READ UNCOMMITTED');
            
            // Primera transacción
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("UPDATE productos SET precio = precio * 1.1 WHERE id = ?");
            $stmt->execute([1]);
            
            // Simular un retraso
            sleep(2);
            
            // Rollback de la primera transacción
            $this->pdo->rollBack();
            
            echo "Transacción revertida<br>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    // Ejemplo de transacción con lectura repetible
    public function demonstrateRepeatableRead() {
        try {
            $this->setIsolationLevel('REPEATABLE READ');
            
            $this->pdo->beginTransaction();
            
            // Primera lectura
            $stmt = $this->pdo->query("SELECT precio FROM productos WHERE id = 1");
            $precio1 = $stmt->fetchColumn();
            
            // Simular un retraso
            sleep(2);
            
            // Segunda lectura
            $stmt = $this->pdo->query("SELECT precio FROM productos WHERE id = 1");
            $precio2 = $stmt->fetchColumn();
            
            echo "Primera lectura: $precio1<br>";
            echo "Segunda lectura: $precio2<br>";
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
}

// Ejemplo de uso
$tm = new TransactionManager($pdo);
$tm->demonstrateDirtyRead();
$tm->demonstrateRepeatableRead();
?>
        