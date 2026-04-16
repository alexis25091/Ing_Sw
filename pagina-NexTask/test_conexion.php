
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "127.0.0.1";
$usuario = "nexuser";   // nuevo usuario
$password = "123456";    // contraseña del usuario
$bd = "nexTask";

$conn = new mysqli($host, $usuario, $password, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    echo "¡Conexión exitosa a la base de datos nexTask!";
}
?>

