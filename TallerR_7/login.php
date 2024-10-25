<?php
include 'config_sesion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error de validación CSRF");
    }
    // ... resto del código de login ...
}

// Generar token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!-- En el formulario HTML -->
<form method="post" action="">
    <!-- ... otros campos ... -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="submit" value="Iniciar Sesión">
</form>
       

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php
    if (isset($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    ?>
    <form method="post" action="">
        <label for="usuario">Usuario:</label><br>
        <input type="text" id="usuario" name="usuario" required><br><br>
        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br><br>
        <input type="submit" value="Iniciar Sesión">
    </form>
</body>
</html>