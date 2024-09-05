<?php
include 'utilidades_texto.php';

$frases = [
    "La vida es bonita",
    "PHP es un lenguaje de programación bieen pritty loco ",
    "Aprender a programar es divertido mas si lo haces para hackear"
];

echo "<table border='1' style='border-collapse: collapse; width: 70%;'>";
echo "<tr><th>Frase Original</th><th>Número de Palabras</th> <th>Número de Vocales</th><th>Palabras Invertidas</th></tr>";

foreach ($frases as $frase) {
    $numPalabras = contar_palabras($frase);
    $numVocales = contar_vocales($frase);
    $fraseInvertida = invertir_palabras($frase);



    echo "<tr>";
    echo "<td>$frase</td>";
    echo "<td>$numPalabras</td>";
    echo "<td>$numVocales</td>";
    echo "<td>$fraseInvertida</td>";
    echo "</tr>";
}

echo "</table>";
?>
