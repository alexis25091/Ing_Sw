<?php
    // Inicia sesión y obtiene usuario
    session_start();
    $user_id = $_SESSION['user_id'];

    // Conexión a la base de datos
    include "conexion.php";

    // Obtiene datos del formulario
    $id = $_POST['id'] ?? null;
    $materia_id = intval($_POST['materia']);
    $dificultad = $_POST['dificultad'];
    $fecha = $_POST['fecha_limite'];
    $hora = $_POST['hora_limite'];
    $detalles = $_POST['detalles'];
    $estado = strtolower(trim($_POST['estado']));

    // Une fecha y hora
    $fecha_completa = $fecha . " " . $hora;

    // Si existe ID → editar tarea
    if (!empty($id)) {

        $sql = "UPDATE tareas SET 
            materia_id=?,
            dificultad=?,
            fecha_limite=?,
            detalles=?,
            estado=?
            WHERE id=? AND user_id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssii", $materia_id, $dificultad, $fecha_completa, $detalles, $estado, $id, $user_id);
        $stmt->execute();

        $msg = "editado"; // Para mostrar toast

    } else {

        // Crear nueva tarea
        $sql = "INSERT INTO tareas (materia_id, dificultad, fecha_limite, detalles, estado, user_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssi", $materia_id, $dificultad, $fecha_completa, $detalles, $estado, $user_id);
        $stmt->execute();

        $msg = "guardado"; // Crear nueva tarea
    }
    // Para mostrar toast
    $conn->close();

    // Cierra conexión
    header("Location: ../home.php?msg=" . $msg);
    exit;
?>