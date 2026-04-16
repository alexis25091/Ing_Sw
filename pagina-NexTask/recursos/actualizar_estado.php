<?php
    // Verifica sesión activa
    session_start();

    // Valida sesión
    if(!isset($_SESSION['user_id'])) exit;
    
    // Usa el valor
    $user_id = $_SESSION['user_id'];

    // Conexión a la base de datos
    include "conexion.php";

    // Procesa solo solicitudes POST
    if($_SERVER['REQUEST_METHOD']==='POST'){

        $id = intval($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';

        // Valida datos permitidos
        if($id && in_array($estado,['asignadas','enproceso','terminadas'])){

            // Actualiza estado de la tarea
            $stmt = $conn->prepare("UPDATE tareas SET estado=? WHERE id=? AND user_id=?");
            $stmt->bind_param("sii",$estado,$id,$user_id);
            $stmt->execute();
        }
    }

    // Cierra conexión
    $conn->close();
?>