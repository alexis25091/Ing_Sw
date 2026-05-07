<?php
    // Inicia sesión y obtiene usuario
    session_start();
    $user_id = $_SESSION['user_id'];

    // Conexión a la base de datos
    include "conexion.php";

    // Obtiene datos del formulario
    $id = $_POST['id'] ?? null;
    $materia_id = intval($_POST['materia']);
    $dificultad = intval($_POST['dificultad']);
    
    // --- NUEVAS VARIABLES WSJF ---
    $peso = intval($_POST['peso']);
    $riesgo = intval($_POST['riesgo']);
    // -----------------------------

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
            peso=?,
            riesgo=?,
            fecha_limite=?,
            detalles=?,
            estado=?
            WHERE id=? AND user_id=?";

        $stmt = $conn->prepare($sql);
        
        // Se cambió a "iiiisssii" (4 enteros, 3 strings, 2 enteros)
        $stmt->bind_param("iiiisssii", $materia_id, $dificultad, $peso, $riesgo, $fecha_completa, $detalles, $estado, $id, $user_id);
        $stmt->execute();

        $msg = "editado"; // Para mostrar toast

    } else {

        // Crear nueva tarea
        $sql = "INSERT INTO tareas (materia_id, dificultad, peso, riesgo, fecha_limite, detalles, estado, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        
        // Se cambió a "iiiisssi" (4 enteros, 3 strings, 1 entero)
        $stmt->bind_param("iiiisssi", $materia_id, $dificultad, $peso, $riesgo, $fecha_completa, $detalles, $estado, $user_id);
        $stmt->execute();

        $msg = "guardado"; // Crear nueva tarea
    }
    
    // Cierra conexión
    $stmt->close();
    $conn->close();

    // Redirige al inicio con mensaje
    header("Location: ../home.php?msg=" . $msg);
    exit;
?>