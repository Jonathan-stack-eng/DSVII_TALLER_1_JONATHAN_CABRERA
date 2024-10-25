<?php
session_start();

$usuarios = [
    'admin' => '1234',
    'usuario1' => 'password'
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    if (isset($usuarios[$usuario]) && $usuarios[$usuario] === $password) {
        $_SESSION['usuario'] = $usuario;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Credenciales inválidas.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <label>Usuario: <input type="text" name="usuario" required></label><br>
        <label>Contraseña: <input type="password" name="password" required></label><br>
        <input type="submit" value="Iniciar sesión">
    </form>
</body>
</html>
