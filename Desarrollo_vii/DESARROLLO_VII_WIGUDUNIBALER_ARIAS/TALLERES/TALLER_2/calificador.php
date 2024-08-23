<?php
  $calificacion = rand(0,100);
  $letra;

  if ($calificacion >= 90) {
    $letra = "A";
} elseif ($calificacion >= 80) {
    $letra = "B";
} elseif ($calificacion >= 70) {
    $letra = "C";
} elseif ($calificacion >= 60) {
    $letra = "D";
} else {
    $letra = "F";
}

echo $calificacion . "<br>";
echo "Tu calificacion es $letra <br>";

$resultadoTernario = ($letra == "F") ? "Reprobador" : "Reprobado";
echo "$resultadoTernario<br><br>";

switch ($letra) {
    case "A":
        echo "Excelente trabajo.<br>";
        break;
    case "B":
        echo "Buen trabajo.<br>";
        break;
    case "C":
        echo "Trabajo aceptable.<br>";
        break;
    case "D":
        echo "Necesitas mejorar.<br>";
        break;
    default:
        echo "Debes esforzarte más.<br>";
}
echo "<br>";
?>