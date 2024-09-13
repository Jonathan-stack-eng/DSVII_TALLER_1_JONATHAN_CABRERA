<?php
$productos = [
    'camisa' => 50,
    'pantalon' => 70,
    'zapatos' => 80,
    'calcetines' => 10,
    'gorra' => 25
];

$carrito = [
    'camisa' => 2,
    'pantalon' => 1,
    'zapatos' => 1,
    'calcetines' => 3,
    'gorra' => 0
];

$subtotal = 0;
foreach ($carrito as $producto => $cantidad) {
    if ($cantidad > 0) {
        $precio = $productos[$producto];
        $subtotal += $precio * $cantidad;
    }
}

$descuento = calcular_descuento($subtotal);
$impuesto = aplicar_impuesto($subtotal);
$total = calcular_total($subtotal, $descuento, $impuesto);

echo "<h2>Resumen de la Compra</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 70%;'>";
echo "<tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Precio Total</th></tr>";

foreach ($carrito as $producto => $cantidad) {
    if ($cantidad > 0) {
        $precio = $productos[$producto];
        $precioTotal = $precio * $cantidad;
        echo "<tr>";
        echo "<td>$producto</td>";
        echo "<td>$cantidad</td>";
        echo "<td>$$precio</td>";
        echo "<td>$$precioTotal</td>";
        echo "</tr>";
    }
}

echo "</table>";
echo "<p><strong>Subtotal:</strong> $$subtotal</p>";
echo "<p><strong>Descuento Aplicado:</strong> $$descuento</p>";
echo "<p><strong>Impuesto (7%):</strong> $$impuesto</p>";
echo "<p><strong>Total a Pagar:</strong> $$total</p>";
?>
