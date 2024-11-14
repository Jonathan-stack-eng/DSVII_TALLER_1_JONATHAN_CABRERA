<?php
require_once 'config.php';
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user']['id'])) {
    header("Location: google_login.php");
    exit();
}

$userId = $_SESSION['user']['id'];


// Cargar los libros guardados del usuario desde la base de datos
$_SESSION['biblioteca'] = [];
$stmt = $pdo->prepare("SELECT google_books_id, titulo, autor, imagen_portada, reseña_personal FROM libros_guardados WHERE user_id = ?");
$stmt->execute([$userId]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $_SESSION['biblioteca'][$row['google_books_id']] = $row;
}

// Procesar las solicitudes de agregar o eliminar libros
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar'])) {
        // Guardar el libro en la base de datos
        $libro = [
            'google_books_id' => $_POST['google_books_id'],
            'titulo' => $_POST['titulo'],
            'autor' => $_POST['autor'],
            'imagen_portada' => $_POST['imagen_portada'],
            'reseña_personal' => $_POST['reseña'] ?? ''
        ];
        
        // Insertar o actualizar en la base de datos
        $stmt = $pdo->prepare("INSERT INTO libros_guardados (user_id, google_books_id, titulo, autor, imagen_portada, reseña_personal) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE reseña_personal = ?");
        $stmt->execute([$userId, $libro['google_books_id'], $libro['titulo'], $libro['autor'], $libro['imagen_portada'], $libro['reseña_personal'], $libro['reseña_personal']]);

        // Actualizar datos en la sesión
        $_SESSION['biblioteca'][$libro['google_books_id']] = $libro;
    } elseif (isset($_POST['eliminar'])) {
        $google_books_id = $_POST['google_books_id'];
        
        // Eliminar de la base de datos
        $stmt = $pdo->prepare("DELETE FROM libros_guardados WHERE user_id = ? AND google_books_id = ?");
        $stmt->execute([$userId, $google_books_id]);

        // Eliminar del array de la sesión
        unset($_SESSION['biblioteca'][$google_books_id]);
    }
}

// Obtener los libros guardados en la sesión
$libros = $_SESSION['biblioteca'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca Personal</title>
    <style>
        .libro { border: 1px solid #ccc; padding: 10px; margin: 10px; }
        .libro img { max-width: 100px; display: block; }
    </style>
</head>
<body>
    <h1>Dashboard - Biblioteca Personal</h1>
    
    <!-- Formulario para buscar libros usando la API -->
    <h2>Buscar Libros</h2>
    <form method="post" action="">
        <input type="text" name="titulo" placeholder="Ingresa el título de un libro..." required>
        <button type="submit" name="buscar">Buscar</button>
    </form>

    <!-- Sección para mostrar resultados -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
        $titulo = urlencode($_POST['titulo']);
        $url = "https://www.googleapis.com/books/v1/volumes?q=$titulo";
        $response = file_get_contents($url);
        $resultado = json_decode($response, true);

        if (!empty($resultado['items'])) {
            echo "<h2>Resultados de Búsqueda</h2>";
            foreach ($resultado['items'] as $item) {
                $titulo = $item['volumeInfo']['title'] ?? 'Título desconocido';
                $autor = !empty($item['volumeInfo']['authors']) ? implode(', ', $item['volumeInfo']['authors']) : 'Autor desconocido';
                $imagen = $item['volumeInfo']['imageLinks']['thumbnail'] ?? 'sin_imagen.png';
                $google_books_id = $item['id'];
                echo "<div class='libro'>";
                echo "<h3>" . htmlspecialchars($titulo) . "</h3>";
                echo "<p>Autor: " . htmlspecialchars($autor) . "</p>";
                echo "<img src='" . htmlspecialchars($imagen) . "' alt='Portada del libro'>";
                echo "<form method='post'>
                        <input type='hidden' name='google_books_id' value='" . htmlspecialchars($google_books_id) . "'>
                        <input type='hidden' name='titulo' value='" . htmlspecialchars($titulo) . "'>
                        <input type='hidden' name='autor' value='" . htmlspecialchars($autor) . "'>
                        <input type='hidden' name='imagen_portada' value='" . htmlspecialchars($imagen) . "'>
                        <button type='submit' name='guardar'>Guardar en mi biblioteca</button>
                      </form>";
                echo "</div>";
            }
        } else {
            echo "<p>No se encontraron resultados para '$titulo'.</p>";
        }
    }
    ?>

    <!-- Sección para mostrar los libros guardados -->
    <h2>Tu Biblioteca Personal</h2>
    <?php if (empty($libros)): ?>
        <p>No tienes libros guardados en tu biblioteca.</p>
    <?php else: ?>
        <div class="libros">
            <?php foreach ($libros as $libro): ?>
                <div class="libro">
                    <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
                    <p>Autor: <?php echo htmlspecialchars($libro['autor']); ?></p>
                    <img src="<?php echo htmlspecialchars($libro['imagen_portada']); ?>" alt="Portada del libro">
                    <form method="post">
                        <input type="hidden" name="google_books_id" value="<?php echo htmlspecialchars($libro['google_books_id']); ?>">
                        <button type="submit" name="eliminar">Eliminar de mi biblioteca</button>
                    </form>
                    <p>Reseña: <?php echo htmlspecialchars($libro['reseña_personal']); ?> <input type="textbox"></p> <button type="submit" name="guardarReseña">Guardar Reseña</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
