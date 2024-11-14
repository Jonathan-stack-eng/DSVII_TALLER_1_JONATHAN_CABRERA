<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        // Encriptar la contraseña
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (name, email, password) 
            VALUES (:name, :email, :password)
        ");
        $stmt->execute([
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':password' => $passwordHash
        ]);

        header('Location: ../auth/login.php');
        exit();
    } else {
        echo "Error: Faltan datos obligatorios para registrar el usuario.";
    }
}
?>
