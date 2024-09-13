<?php
function contar_palabras($texto) {
    $palabras = str_word_count($texto, 0);
    return $palabras;
}

function contar_vocales($texto) {
    $vocales = preg_match_all('/[aeiouAEIOU]/', $texto);
    return $vocales;
}

function invertir_palabras($texto) {
    $palabras = explode(' ', $texto);
    $palabrasInvertidas = array_reverse($palabras);
    return implode(' ', $palabrasInvertidas);
}
?>


?>