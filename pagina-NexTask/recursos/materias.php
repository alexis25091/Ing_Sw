<?php
    // Inicia sesión y conexión
    session_start();
    include "conexion.php";

    // ID del usuario
    $user_id = $_SESSION['user_id'];

    /* CREAR / EDITAR */
    if(isset($_POST['nombre'])){
        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']); // limpia espacios
        $color = $_POST['color'];
        $dificultad = intval($_POST['dificultad'] ?? 1); // 👈 NUEVO

        // Validar que no estén vacíos
        if($nombre === "" || $color === ""){
            header("Location: ../perfil.php");
            exit;
        }

        // Validar rango de dificultad
        if($dificultad < 1 || $dificultad > 10){
            header("Location: ../perfil.php");
            exit;
        }

        // EDITAR
        if($id){
            $stmt = $conn->prepare("
                UPDATE materias 
                SET nombre=?, color=?, dificultad=? 
                WHERE id=? AND user_id=?
            ");
            $stmt->bind_param("ssiii", $nombre, $color, $dificultad, $id, $user_id);
            $stmt->execute();

            header("Location: ../perfil.php?materia=edit_ok");
        } 
        
        // CREAR / RESTAURAR
        else{

            // Buscar si ya existe (aunque esté eliminada)
            $stmt = $conn->prepare("
                SELECT id, estado 
                FROM materias 
                WHERE LOWER(nombre)=LOWER(?) 
                AND user_id=?
            ");
            $stmt->bind_param("si", $nombre, $user_id);
            $stmt->execute();
            $buscar = $stmt->get_result();

            if($buscar->num_rows > 0){
                $materia = $buscar->fetch_assoc();

                if($materia['estado'] == 0){
                    // EXISTE PERO ESTÁ ELIMINADA → RESTAURAR
                    $stmt = $conn->prepare("
                        UPDATE materias 
                        SET estado=1, color=?, dificultad=? 
                        WHERE id=?
                    ");
                    $stmt->bind_param("sii", $color, $dificultad, $materia['id']);
                    $stmt->execute();

                    header("Location: ../perfil.php?materia=restored");

                } else {
                    // YA EXISTE ACTIVA
                    header("Location: ../perfil.php?materia=exists");
                }

            } else {
                // NO EXISTE → CREAR NUEVA
                $stmt = $conn->prepare("
                    INSERT INTO materias (user_id,nombre,color,dificultad) 
                    VALUES (?,?,?,?)
                ");
                $stmt->bind_param("issi", $user_id, $nombre, $color, $dificultad);
                $stmt->execute();

                header("Location: ../perfil.php?materia=add_ok");
            }
        }

        exit;
    }

    // ELIMINAR INTELIGENTE
    if(isset($_POST['delete'])){
        $id = $_POST['delete'];

        // Verificar si la materia tiene tareas
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM tareas 
            WHERE materia_id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $check = $stmt->get_result();
        $row = $check->fetch_assoc();

        if($row['total'] > 0){
            // Tiene tareas → solo ocultar
            $stmt = $conn->prepare("
                UPDATE materias 
                SET estado=0 
                WHERE id=? AND user_id=?
            ");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();

        } else {
            // NO tiene tareas → eliminar completamente
            $stmt = $conn->prepare("
                DELETE FROM materias 
                WHERE id=? AND user_id=?
            ");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
        }

        header("Location: ../perfil.php?materia=delete_ok");
        exit;
    }

    // Redirección por defecto
    header("Location: ../perfil.php");
?>