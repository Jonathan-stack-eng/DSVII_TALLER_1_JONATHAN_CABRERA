<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['tareas'])) {
    $_SESSION['tareas'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $fecha_limite = $_POST['fecha_limite'];
    $errores = [];

    if (empty($titulo)) {
        $errores[] = "El título es obligatorio.";
    }

    if (empty($fecha_limite) || strtotime($fecha_limite) <= time()) {
        $errores[] = "La fecha límite debe ser válida y futura.";
    }

    if (empty($errores)) {
        $_SESSION['tareas'][] = [
            'titulo' => $titulo,
            'fecha_limite' => $fecha_limite
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?></h2>
    <a href="logout.php">Cerrar sesión</a>

    <h3>Tus tareas</h3>
    <ul>
        <?php foreach ($_SESSION['tareas'] as $tarea): ?>
            <li><?php echo $tarea['titulo'] . " - " . $tarea['fecha_limite']; ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Agregar nueva tarea</h3>
    <?php if (!empty($errores)): ?>
        <ul style="color: red;">
            <?php foreach ($errores as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="dashboard.php">
        <label>Título de la tarea: <input type="text" name="titulo" required></label><br>
        <label>Fecha límite: <input type="date" name="fecha_limite" required></label><br>
        <input type="submit" value="Agregar tarea">
    </form>
</body>
</html>
