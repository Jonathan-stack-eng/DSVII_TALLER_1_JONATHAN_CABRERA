<?php
$nombre = "WIGUDUNIBALER";
$edad = 21;
$correo = "wigudunibalera@gmail.com";
$telefono = 66847798;

// Definición de constante
define("OCUPACION", "Estudiante");

// Creación de mensaje usando diferentes métodos de concatenación e impresión
$parrafo1 = "Hola, mi nombre es " . $nombre . " y tengo " . $edad . " años.". " mi correo eletronico es " . $correo . " mi numero de telefono es " . $telefono . " mi ocupacion es " . OCUPACION . ".";

echo $parrafo1 . "<br>";
print($parrafo1 . "<br>");
printf("Hola, mi nombre es %s y tengo %d años. mi correo eletronico es %s mi numero de telefono es %d mi ocupacion es %s.",$nombre,$edad,$correo,$telefono, OCUPACION )

?>