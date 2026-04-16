<?php
    // Inicia sesión para acceder al usuario
    session_start(); 

    // Verifica si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.html"); // Redirige si no hay sesión
        exit;
    }

    // Conexión a la BD
    include "./conexion.php";

    // Verifica si se recibió el ID de la tarea
    if (isset($_POST['tarea_id'])) {

        $user_id = $_SESSION['user_id']; // Usuario actual
        $tarea_id = intval($_POST['tarea_id']); // ID de tarea 

        // Elimina solo si la tarea pertenece al usuario
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tarea_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close(); // Cierra conexión

    echo "ok"; // Respuesta para fetch
?>