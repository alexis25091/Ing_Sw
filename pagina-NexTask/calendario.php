<?php
    // Control de sesion
    session_start();

    // Si no hay sesión iniciada, redirige al login
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.html");
        exit;
    }

    // Conexion a la bdd
    include "./recursos/conexion.php";
    // Guardamos el ID del usuario (por si luego se filtran datos por usuario)
    $user_id = $_SESSION['user_id'];

    // Consulta a la bdd
    $sql = "
    SELECT t.*, m.color, m.nombre AS materia_nombre
    FROM tareas t
    LEFT JOIN materias m ON t.materia_id = m.id
    WHERE t.user_id = $user_id
    ";
    $result = $conn->query($sql);

    // Guardamos los resultados en un array
    $tareas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Convertir estado a texto bonito
            switch($row['estado']) {
                case 'asignadas':
                    $row['estado_legible'] = 'Asignada';
                    break;
                case 'enproceso':
                    $row['estado_legible'] = 'En proceso';
                    break;
                case 'terminadas':
                    $row['estado_legible'] = 'Terminada';
                    break;
                case 'noterminada':
                    $row['estado_legible'] = 'No terminada';
                    break;
                default:
                    $row['estado_legible'] = $row['estado'];
            }
            $tareas[] = $row;
        }
    }

    // Cerramos la conexión
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NexTask - Calendario</title>
    <link rel="stylesheet" href="./estilos/template.css" />
    <link rel="stylesheet" href="./estilos/calendario.css" />
</head>
<body>

    <!-- Menu superior -->
    <header class="navbar">
        <!-- Logo -->
        <div class="logo_completo">
            <a href="home.php">
                <img src="./imagenes/LogoCompleto.png" alt="logo" class="logo">
            </a>
        </div>

        <!-- Navegacion principal -->
        <nav>
            <a href="home.php">Inicio</a>
            <a href="calendario.php" class="activo">Calendario</a>
            <a href="historial.php">Historial</a>
        </nav>

        <!-- Usuario + menú desplegable -->
        <div class="usuario" id="usuario-menu">
            <span class="toggle-usuario">
                <!-- Se muestra el nombre del usuario de forma segura -->
                <?php echo htmlspecialchars(explode(" ", $_SESSION['user_nombre'])[0]); ?>
            </span>

            <ul class="submenu" id="submenu-usuario">
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="./recursos/salir_sesion.php">Salir</a></li>
            </ul>
        </div>
    </header>

    <!-- Calendario -->
    <div class="calendar-container">
        <!-- Cabecera del calendario con navegación de meses -->
        <div class="calendar-header">
            <button id="prev-month">← Mes anterior</button>
            <div id="current-month-label"></div>
            <button id="next-month">Mes siguiente →</button>
        </div>

        <!-- Días de la semana -->
        <div class="calendar-weekdays">
            <div>Dom</div>
            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div>Sáb</div>
        </div>

        <!-- Contenedor de los días del mes -->
        <div class="calendar-grid" id="calendar-grid"></div>

        <!-- Imagen fija si no hay tareas -->
        <div class="no-tasks-image" id="no-tasks-image" style="display:none;">
            <img src="./imagenes/no_tasks.png" alt="No hay tareas" />
        </div>
    </div>

    <!-- Modal de detalle de tareas -->
    <div id="modal-detalle">
        <div class="modal-content">
            <!-- Botón de cerrar -->
            <span class="cerrar" id="cerrar-modal-detalle">&times;</span>

            <h3>Detalle de la tarea</h3>
            <div><label>Materia:</label> <div class="valor" id="detalle-materia"></div></div>
            <div><label>Fecha límite:</label> <div class="valor" id="detalle-fecha"></div></div>
            <div><label>Detalles:</label> <div class="valor" id="detalle-detalles"></div></div>
            <div><label>Dificultad:</label> <div class="valor" id="detalle-dificultad"></div></div>
            <div><label>Estado:</label> <div class="valor" id="detalle-estado"></div></div>
        </div>
    </div>

    <!-- Pasamos las tareas de PHP a JS -->
    <script> const tareas = <?php echo json_encode($tareas); ?>; </script>
    <script src="./script/calendario.js"></script>

</body>
</html>