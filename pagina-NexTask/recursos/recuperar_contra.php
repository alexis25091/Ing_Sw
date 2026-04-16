<?php
    // Conexión a la base de datos
    include "conexion.php";

    // Solo procesa si viene del formulario (POST)
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        // Captura y limpia datos
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);

        // Busca la contraseña del usuario con nombre y correo
        $sql = "SELECT password FROM usuarios WHERE nombre = ? AND email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nombre, $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Si encuentra el usuario
        if($resultado->num_rows === 1){

            $usuario = $resultado->fetch_assoc();
            // Redirige al index con la contraseña
            header("Location: ../index.html?recuperar=ok&pass=" . urlencode($usuario['password']));

        } else {
            // Datos incorrectos
            header("Location: ../index.html?recuperar=error");
        }

        $stmt->close(); // Cierra consulta
        $conn->close(); // Cierra conexión
        exit;
    }
?>