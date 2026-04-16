<?php
    // Control de sesión
    session_start();

    // Si no hay sesión iniciada, redirige al login
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.html");
        exit;
    }

    // Conexión a la BDD
    include "./recursos/conexion.php";

    // Guardamos el ID del usuario
    $user_id = $_SESSION['user_id'];

    // Fecha de hoy
    $hoy = date("Y-m-d");

    // Actualizar tareas vencidas
    $ahora = new DateTime("now", new DateTimeZone('America/Mexico_City'));
    $ahora_str = $ahora->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        UPDATE tareas 
        SET estado='noterminada' 
        WHERE user_id=? 
        AND estado != 'terminadas'
        AND fecha_limite < ?
    ");
    $stmt->bind_param("is", $user_id, $ahora_str);
    $stmt->execute();

    // Consulta a la BDD
    $sql = "SELECT tareas.*, materias.color, materias.nombre AS materia_nombre 
            FROM tareas 
            LEFT JOIN materias 
            ON tareas.materia_id = materias.id 
            WHERE tareas.user_id = $user_id";
    $result = $conn->query($sql);

    // Estructura tipo Kanban (columnas por estado)
    $tareas = ['asignadas'=>[], 'enproceso'=>[], 'terminadas'=>[]];

    // Fecha actual (para filtrar tareas)
    $hoy = new DateTime("now", new DateTimeZone('America/Mexico_City'));
    $hoy_str = $hoy->format('Y-m-d');

    // Filtrado y organización del Kanban
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            // Aseguramos que siempre haya color
            $row['color'] = $row['color'] ?? '#ccc';

            // Convertimos la fecha de la tarea a DateTime
            $fecha_tarea = new DateTime($row['fecha_limite'], new DateTimeZone('America/Mexico_City'));
            $fecha_tarea_str = $fecha_tarea->format('Y-m-d');

            // Mostrar todas las tareas cuya fecha límite sea hoy o después
            if ($fecha_tarea_str >= $hoy_str) {
                $tareas[$row['estado']][] = $row;
            }
            
        }
    }

    // Se obtienen solo las materias del usuario actual
    $sql_materias = "SELECT * FROM materias WHERE user_id = $user_id AND estado = 1";
    $result_materias = $conn->query($sql_materias);

    // Cerramos la conexión
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inicio</title>
    <link rel="stylesheet" href="./estilos/home.css">
    <link rel="stylesheet" href="./estilos/template.css"> 
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
            <a href="home.php" class="activo">Inicio</a>
            <a href="calendario.php">Calendario</a>
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


    <!-- Tablero kanban -->
    <main class="kanban">
        <!-- Recorre los 3 estados (columnas) -->
        <?php foreach(['asignadas'=>'Asignadas','enproceso'=>'En proceso','terminadas'=>'Terminadas'] as $estado=>$titulo): ?>
        <section class="columna">
            <!-- Título de columna -->
            <h2><?php echo $titulo; ?></h2>

            <div class="lista-tareas">
                <!-- Si no hay tareas -->
                <?php if (empty($tareas[$estado])): ?>
                    <p class="sin-tareas">No hay actividades</p>
                <?php else: ?>
                 <!-- Tarjetas de tareas -->
                <?php foreach($tareas[$estado] as $t): ?>
                    <div class="card"
                        style="border-left: 6px solid <?= $t['color'] ?>; background: <?= $t['color'] ?>22;"
                        data-id="<?php echo $t['id']; ?>"
                        data-materia="<?php echo htmlspecialchars($t['materia_nombre'] ?? 'Materia eliminada'); ?>"
                        data-materia-id="<?php echo $t['materia_id']; ?>" 
                        data-dificultad="<?php echo $t['dificultad']; ?>"
                        data-fecha="<?php echo $t['fecha_limite']; ?>"
                        data-detalles="<?php echo htmlspecialchars($t['detalles']); ?>"
                        data-estado="<?php echo $t['estado']; ?>">

                        <!-- Contenido visible -->
                        <h3><?php echo htmlspecialchars($t['materia_nombre'] ?? 'Materia eliminada'); ?></h3>
                        <p>Dificultad: <?php echo $t['dificultad']; ?>/10</p>

                        <!-- Formato de fecha -->
                        <?php $fecha = new DateTime($t['fecha_limite']); ?>
                        <p>Hora limite: <?php echo $fecha->format('H:i'); ?></p>
                        <p>Dia: <?php echo $fecha->format('d-m-Y'); ?></p>

                    </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        <?php endforeach; ?>
    </main>

    <!-- Botorn de agregar tarea -->
    <button class="btn-agregar" id="btn-agregar">+ Nueva tarea</button>

    <!-- Modal del formulario -->
    <div class="modal" id="modal-form">
        <div class="modal-content">
            <span class="cerrar" id="cerrar-form">&times;</span>

            <h3 id="titulo-form">Agregar nueva tarea</h3>
            <p>Las materias se agregan o actualizan en tu perfil</p>
            <form id="form-tarea" class="form-tarea form-grid" action="./recursos/guardar_tarea.php" method="POST">
                
                <!-- ID oculto (para editar tarea existente) -->    
                <input type="hidden" name="id">

                <div class="seccion">

                    <!-- Materias dinámicas desde BD -->
                    <label>Materia: </label>
                        <select name="materia" required>
                            <option value="" disabled selected>Elige una opción</option>
                            <?php while($m = $result_materias->fetch_assoc()): ?>
                                <option value="<?php echo $m['id']; ?>">
                                    <?php echo $m['nombre']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    <label>Detalles: </label>
                        <textarea name="detalles" placeholder="Escribe aquí los detalles de tu tarea..."></textarea>
                    <label>Estado: </label>
                        <select name="estado" required>
                            <option value="" disabled selected>Elige una opción</option>
                            <option value="asignadas">Asignadas</option>
                            <option value="enproceso">En proceso</option>
                            <option value="terminadas">Terminadas</option>
                        </select>    
                </div>

                <!-- Seccion fecha y dificultad -->
                <div class="seccion">
                    <label>Fecha límite: </label><input type="date" name="fecha_limite" required />
                    <label>Hora límite (formato 24hrs): </label>
                        <input type="time" name="hora_limite" required /> 
                    <!-- Slider de dificultad -->
                    <div class="dificultad-container">
                        <label>Nivel de dificultad: <span class= "valor-dificultad" id="valor-dificultad"> 1 - 10 </span> </label>
                        <input type="range" name="dificultad" min="1" max="10" id="range-dif" data-movido="false">
                    </div>
                </div>

                <!-- Botones -->
                <div class="botones-form botones-full">
                    <button type="button" id="btn-cancelar" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="button" id="btn-eliminar" class="btn-eliminar" style="display:none;"> Eliminar tarea</button>
                    <button type="submit" class="btn-submit">Guardar</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal de cambio de estado -->
    <div class="modal" id="modal-estado">
        <div class="modal-content">
             <!-- Cerrar -->
            <span class="cerrar" id="cerrar-estado">&times;</span>
            
            <!-- Información dinámica -->
            <button type="button" id="editar-tarea-modal">Editar</button>
            <h3 id="nombre-tarea"></h3>
            <p id="detalle-tarea"></p>
            
            <!-- Botones para cambiar estado -->
            <button class="estado-btn" data-estado="asignadas">Asignadas</button>
            <button class="estado-btn" data-estado="enproceso">En proceso</button>
            <button class="estado-btn" data-estado="terminadas">Terminadas</button>
        </div>
    </div>

    <!-- Modal para confirmar eliminación de tarea -->
    <div id="modal-eliminar" class="modal">
        <div class="modal-content">
            <span class="cerrar" onclick="cerrarModalEliminar()">&times;</span>

            <h3>¿Estás seguro de eliminar la tarea de la materia "<span id="nombre-tarea-eliminar"></span>"?</h3>

            <form id="form-eliminar" method="POST" action="./recursos/eliminar_tarea.php">    
                <input type="hidden" name="tarea_id" id="tarea_id_eliminar">

                <div class="botones-form botones-full">
                    <button type="button" onclick="cerrarModalEliminar()">No</button>
                    <button type="button" onclick="confirmarEliminar()">Sí</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Notificación flotante tipo toast -->
    <div id="toast-kanban" class="toast"></div>

    <script src="./script/home.js"></script>

</body>
</html>