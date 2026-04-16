<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $host = "127.0.0.1";
    $usuario = "root";   // usuario de MySQL
    $password = "";    // contraseña
    $bd = "nexTask";

    // Crea una nueva conexión a MySQL usando mysql
    $conn = new mysqli($host, $usuario, $password, $bd);

    // Verifica si hubo un error al conectar y detiene la ejecución mostrando el mensaje
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
?>
