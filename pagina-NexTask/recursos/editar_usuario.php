<?php
    // Inicia sesión y valida acceso
    session_start();
    if(!isset($_SESSION['user_id'])) exit;

    // Conexión a la BD
    include "conexion.php";

    // Datos del usuario y formulario
    $user_id = $_SESSION['user_id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $pass_actual = $_POST['password_actual'];
    $pass_nueva = $_POST['password_nueva'];

    // Limpiar espacios
    $nombre = trim($nombre);
    $email = trim($email);

    // Validar que no estén vacíos
    if(empty($nombre) || empty($email)){
        header("Location: ../perfil.php?error=vacio");
        exit;
    }

    // Validar formato de email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../perfil.php?error=email");
        exit;
    }

    // Obtener datos actuales del usuario
    $res = $conn->query("SELECT * FROM usuarios WHERE id=$user_id");
    $user = $res->fetch_assoc();

    // Detectar cambios
    $cambio_nombre = $nombre !== $user['nombre'];
    $cambio_email = $email !== $user['email'];
    $cambio_pass = false;

    // Si quiere cambiar contraseña
    if(!empty($pass_nueva)){

        // Validar contraseña actual
        if(empty($pass_actual) || $pass_actual !== $user['password']){
            header("Location: ../perfil.php?error=pass");
            exit;
        }

        // Evitar misma contraseña
        if($pass_nueva === $user['password']){
            header("Location: ../perfil.php?error=misma_pass");
            exit;
        }

        // Actualizar todo incluyendo contraseña
        $hash = $pass_nueva;
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $nombre, $email, $hash, $user_id);
        $stmt->execute();

        $cambio_pass = true;

    }else{
        // Actualizar solo nombre y correo
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $nombre, $email, $user_id);
        $stmt->execute();
    }

    // Actualizar nombre en sesión
    $_SESSION['user_nombre'] = $nombre;

    // Redirigir según el cambio realizado
    if($cambio_pass){
        header("Location: ../perfil.php?user=pass_ok");
    } elseif($cambio_nombre){
        header("Location: ../perfil.php?user=nombre_ok");
    } elseif($cambio_email){
        header("Location: ../perfil.php?user=email_ok");
    } else {
        header("Location: ../perfil.php");
    }
