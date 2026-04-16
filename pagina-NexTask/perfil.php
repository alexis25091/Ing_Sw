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
    $user = $conn->query("SELECT * FROM usuarios WHERE id=$user_id")->fetch_assoc();
    $materias = $conn->query("SELECT * FROM materias WHERE user_id = $user_id AND estado = 1");

    // Cerramos la conexión
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil</title>
    <link rel="stylesheet" href="./estilos/perfil.css">
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
            <a href="home.php">Inicio</a>
            <a href="calendario.php">Calendario</a>
            <a href="historial.php">Historial</a>
        </nav>

        <!-- Usuario + menú desplegable -->
        <div class="usuario" id="usuario-menu">
            <span class="toggle-usuario">
                <?php echo htmlspecialchars(explode(" ", $_SESSION['user_nombre'])[0]); ?>
            </span>

            <ul class="submenu" id="submenu-usuario">
                <li><a href="perfil.php" class="activo">Perfil</a></li>
                <li><a href="./recursos/salir_sesion.php">Salir</a></li>
            </ul>
        </div>
    </header>

    <main class="perfil-container">

        <!-- Ver informacion del perfil -->
        <section class="perfil-box">
            <div class="perfil-izq">
                <img src="./imagenes/Usuario.png" class="avatar">
            </div>

            <div class="perfil-der">
                <h2><?= $user['nombre'] ?></h2>
                <p><?= $user['email'] ?></p>
                <button id="btn-editar">Editar datos</button>
            </div>
        </section>

        <!-- Mostrar materias -->
        <section class="materias-box">
            <h3>Editor de Materias</h3>

            <div class="lista-materias">

                <?php if ($materias->num_rows > 0): ?>
                    <?php while($m = $materias->fetch_assoc()): ?>
                        <div class="materia-item"
                            style="border-left: 8px solid <?= $m['color'] ?>;
                                    background: <?= $m['color'] ?>33">
                            <span><?= $m['nombre'] ?></span>

                            <!-- Parte de los botondes de editar materia -->
                            <div>
                                <button onclick="editarMateria(<?= $m['id'] ?>,'<?= $m['nombre'] ?>','<?= $m['color'] ?>',<?= $m['dificultad'] ?>)">✏️</button>
                                <button onclick="eliminarMateria(<?= $m['id'] ?>,'<?= $m['nombre'] ?>')">🗑️</button>
                            </div>
                        </div>

                    <?php endwhile; ?>

                <?php else: ?>
                    <p> No hay materias creadas</p>
                <?php endif; ?>

            </div>

            <button id="btn-agregar">+ Agregar materia</button>
        </section>

        <!-- Salir de sesion -->
        <div class="logout-container">
            <a href="./recursos/salir_sesion.php" class="btn-logout">Cerrar sesión</a>
        </div>

    </main>

    <!-- MODAL USUARIO -->
    <div id="modal-user">
        <div class="modal-content">
            <span class="cerrar" onclick="cerrarModalUser()">&times;</span>
            <form action="./recursos/editar_usuario.php" method="POST">
                <h3>Editar datos</h3>
                <p>Puedes modificar tu nombre o correo</p>
                <div>  
                    <label>Nombre de usuario: </label>
                    <input type="text" name="nombre" value="<?= $user['nombre'] ?>" required>
                </div>

                <div>  
                    <label>Correo electronico: </label>
                    <input type="email" name="email" value="<?= $user['email'] ?>" required>
                </div>
                <br>
                <p>Para cambiar tu contraseña, ingresa la actual y despues la nueva.</p>
                
                <div style="position: relative;">  
                    <label>Contraseña actual: </label>
                    <input type="password" id="pass-actual" name="password_actual" placeholder="Contraseña actual">
                    <span class="toggle-pass" onclick="togglePass('pass-actual', this)">Ver</span>
                </div>

                <div style="position: relative;">  
                    <label>Nueva contraseña: </label>
                    <input type="password" id="pass-nueva" name="password_nueva" placeholder="Nueva contraseña">
                    <span class="toggle-pass" onclick="togglePass('pass-nueva', this)">Ver</span>
                </div>

                <div class="botones">
                    <button type="button" onclick="cerrarModalUser()">Cancelar</button>
                    <button type="submit">Guardar</button>
                </div>
            </form>

        </div>
    </div>

    <!-- MODAL MATERIA -->
    <div id="modal-materia">
        <div class="modal-content">
        <span class="cerrar" onclick="cerrarModalMateria()">&times;</span>
            <form action="./recursos/materias.php" method="POST" class="form-modal">

                <input type="hidden" name="id" id="materia-id">
                <input type="hidden" name="color" id="materia-color">

                <h3>Agregar materia</h3>
                <label>Coloca el nombre de la materia: </label>
                <input type="text" name="nombre" id="materia-nombre" placeholder="Nombre de la materia ">

                <label>Nivel de dificultad: <span id="valor-dificultad-materia">1</span></label>
                <input type="range" name="dificultad" min="1" max="10" id="range-dif-materia">

                <label>Selecciona el color de tu materia: </label>
                    <div class="color-picker">
                        <div class="color-option" data-color="#FF7A90" style="background:#FF7A90"></div> <!-- Rosa fuerte -->
                        <div class="color-option" data-color="#FFB347" style="background:#FFB347"></div> <!-- Naranja -->
                        <div class="color-option" data-color="#FFD700" style="background:#FFD700"></div> <!-- Amarillo -->
                        <div class="color-option" data-color="#7AC7FF" style="background:#7AC7FF"></div> <!-- Celeste -->
                        <div class="color-option" data-color="#4D79FF" style="background:#4D79FF"></div> <!-- Azul -->
                        <div class="color-option" data-color="#7A8C99" style="background:#7A8C99"></div> <!-- Azul grisáceo (primary-100) -->
                        <div class="color-option" data-color="#6FCF97" style="background:#6FCF97"></div> <!-- Verde -->
                        <div class="color-option" data-color="#D36EFF" style="background:#D36EFF"></div> <!-- Morado -->
                        <div class="color-option" data-color="#FF6FCF" style="background:#FF6FCF"></div> <!-- Rosa/magenta -->
                        <div class="color-option" data-color="#FFA500" style="background:#FFA500"></div> <!-- Naranja oscuro -->
                    </div>

                <div class="botones">
                    <button type="button" onclick="cerrarModalMateria()">Cancelar</button>
                    <button type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para confirmar eliminación de materia -->
    <div id="modal-eliminar-materia" class="modal">
        <div class="modal-content">
            <span class="cerrar" onclick="cerrarModalEliminarMateria()">&times;</span>

            <h3>¿Estás seguro de eliminar la materia "<span id="nombre-materia-eliminar"></span>"?</h3>

            <form id="form-eliminar-materia" method="POST" action="./recursos/materias.php">
                <input type="hidden" name="materia_id" id="materia-id-eliminar">

                <div class="botones-form botones-full">
                    <button type="button" onclick="cerrarModalEliminarMateria()">No</button>
                    <button type="button" onclick="confirmarEliminarMateria()">Sí</button>
                </div>
            </form>
        </div>
    </div>


     <!-- Notificación flotante -->
    <div id="toast" class="toast"></div>

    <script src="./script/perfil.js"></script>

</body>
</html>