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

    // Array para mostrar meses en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    // Consulta a la bdd
    $sql = "SELECT tareas.*, materias.nombre AS materia_nombre, materias.color 
            FROM tareas
            LEFT JOIN materias ON tareas.materia_id = materias.id
            WHERE tareas.user_id = $user_id
            ORDER BY tareas.fecha_limite DESC";
    $result = $conn->query($sql);


    // Array donde guardaremos todas las tareas
    $tareas = [];

    // Si hay resultados, los guardamos en el array
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
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
  <title>Historial</title>
  <link rel="stylesheet" href="./estilos/template.css" />
  <link rel="stylesheet" href="./estilos/historial.css" />
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
            <a href="calendario.php">Calendario</a>
            <a href="historial.php" class="activo">Historial</a>
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

    <!-- Contenedor principal -->
    <div class="historial-container">
        <h2>Historial de Tareas</h2>

        <!-- Buscador (filtrado con JS) -->
        <div class="buscador">
            <input type="text" id="buscar" placeholder="Buscar tareas ..." />
        </div>

        <!-- Tabla de tareas -->
        <div class="tabla-scroll">
            <table id="tabla-tareas">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Materia</th>
                        <th>Dificultad</th>
                        <th>Fecha límite</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                
                <!-- Cuerpo dinámico -->
                <tbody>
                    <?php if (count($tareas) === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; font-style: italic; color: var(--text-cafesito);">
                                Aún no tienes tareas para mostrar
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($tareas as $index => $t): ?>
                            <tr data-index="<?php echo $index; ?>" style="background-color: <?php echo $t['color'] ?>33;">
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($t['materia_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($t['dificultad']); ?></td>
                                <td class="fecha">
                                    <?php
                                        $fecha = new DateTime($t['fecha_limite']);
                                        $dia = $fecha->format('d');
                                        $mes = $meses[(int)$fecha->format('m')];
                                        $anio = $fecha->format('Y');
                                        echo "$dia $mes $anio";
                                    ?>
                                </td>
                                <td>
                                    <span class="estado-<?php echo $t['estado']; ?>">
                                        <?php
                                        switch($t['estado']) {
                                            case 'asignadas': echo "Asignada"; break;
                                            case 'enproceso': echo "En proceso"; break;
                                            case 'terminadas': echo "Terminada"; break;
                                            case 'noterminada': echo "No terminada"; break;
                                            default: echo htmlspecialchars($t['estado']);
                                        }
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal - Detalle de la tarea -->
    <div id="modal-detalle">
        <div class="modal-content">

            <!-- Botón cerrar -->
            <span class="cerrar" id="cerrar-modal-detalle">&times;</span>
            
            <h3>Detalle de la tarea</h3>
            <!-- Datos que se llenan dinámicamente con JS -->
            <div><label>Materia:</label> <div class="valor" id="detalle-materia"></div></div>
            <div><label>Fecha límite:</label> <div class="valor" id="detalle-fecha"></div></div>
            <div><label>Detalles:</label> <div class="valor" id="detalle-detalles"></div></div>
            <div><label>Dificultad:</label> <div class="valor" id="detalle-dificultad"></div></div>
            <div><label>Estado:</label> <div class="valor" id="detalle-estado"></div></div>

        </div>
    </div>

    <!-- Pasar datos a JS -->
    <script>const tareas = <?php echo json_encode($tareas); ?>;</script>
    <script src="./script/historial.js"></script>

</body>
</html>
