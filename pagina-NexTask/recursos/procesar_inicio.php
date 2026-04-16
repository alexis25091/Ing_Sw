<?php
    // Inicia sesión para guardar datos del usuario
    session_start(); 

    // Conexión a la base de datos
    include "conexion.php";

    // Solo procesa si viene de un formulario (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Define si es login o registro
        $accion = $_POST['accion']; 

        // REGISTRO 
        if ($accion === "registro") {

            // Captura datos del formulario
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmar = $_POST['confirmarPassword'];

            // Verifica que las contraseñas coincidan
            if ($password !== $confirmar) {
                header("Location: ../index.html?error=confirm&panel=registro");
                exit;
            }

            // $password_hash = password_hash($password, PASSWORD_DEFAULT); // SI SE QUIERE EL HASH EN LA CONTRASEÑA

            // Guarda la contraseña (sin hash por ahora)
            $password_hash = $password;

            // Inserta usuario en la base de datos
            $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nombre, $email, $password_hash);

            if ($stmt->execute()) {
                header("Location: ../index.html?registro=ok");
                exit;
            } else {
                echo "Error: " . $stmt->error; // Muestra error si falla
            }

            $stmt->close();
        }

        //  LOGIN 
        elseif ($accion === "login") {

            // Captura datos
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Busca usuario por email
            $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {

                $usuario = $resultado->fetch_assoc();

                // Verifica contraseña
                if ($password === $usuario['password']) {

                    // Guarda datos en sesión
                    $_SESSION['user_id'] = $usuario['id'];
                    $_SESSION['user_nombre'] = $usuario['nombre'];

                    header("Location: ../home.php"); // Login correcto
                    exit;

                } else {
                    header("Location: ../index.html?error=pass"); // Contraseña incorrecta
                    exit;
                }

            } else {
                header("Location: ../index.html?error=user"); // Usuario incorrecto
                exit;
            }

            $stmt->close();
        }

        // Cerrar conexión
        $conn->close();
    }
?>