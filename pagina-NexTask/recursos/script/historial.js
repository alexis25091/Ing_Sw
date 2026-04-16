document.addEventListener("DOMContentLoaded", () => {

    /*  TABLA DE TAREAS  */
    const tabla = document.getElementById('tabla-tareas');
    const modalDetalle = document.getElementById('modal-detalle');
    const cerrarModalBtn = document.getElementById('cerrar-modal-detalle');
    const inputBuscar = document.getElementById('buscar');

    // Mostrar datos de la tarea en el modal
    function mostrarDetalle(index) {
        const tarea = tareas[index];
        if (!tarea) return;

        document.getElementById('detalle-materia').textContent = tarea.materia_nombre;
        document.getElementById('detalle-dificultad').textContent = tarea.dificultad;

        // Formatear fecha
        document.getElementById('detalle-fecha').textContent =
            new Date(tarea.fecha_limite).toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

        document.getElementById('detalle-detalles').textContent =
            tarea.detalles || "Tarea sin detalles";

        // Traducir estado
        const estados = {
            'asignadas': 'Asignada',
            'enproceso': 'En proceso',
            'terminadas': 'Terminada',
            'noterminada': 'No terminada'
        };

        document.getElementById('detalle-estado').textContent =
            estados[tarea.estado] || tarea.estado;

        // Mostrar modal
        modalDetalle.classList.add('active');
    }

    // Asignar click a cada fila visible
    function asignarEventosClick() {
        tabla.querySelectorAll('tbody tr').forEach(row => {

            // Ignorar filas ocultas
            if (row.style.display === 'none') return;

            row.onclick = () => {
                const idx = row.getAttribute('data-index');
                mostrarDetalle(idx);
            };
        });
    }

    // Cerrar modal con botón
    cerrarModalBtn.addEventListener('click', () => {
        modalDetalle.classList.remove('active');
    });

    // Cerrar modal al hacer click fuera
    modalDetalle.addEventListener('click', (e) => {
        if (e.target === modalDetalle) {
            modalDetalle.classList.remove('active');
        }
    });

    //  BÚSQUEDA EN LA TABLA 
    inputBuscar.addEventListener('input', function() {

        const filtro = this.value.toLowerCase();
        let contador = 1;

        tabla.querySelectorAll('tbody tr').forEach(row => {

            // Ignora la primera columna (número)
            const celdas = Array.from(row.children).slice(1);

            // Une el texto de las columnas
            const texto = celdas.map(c => c.textContent.toLowerCase()).join(' ');

            if (texto.includes(filtro)) {
                row.style.display = '';
                row.children[0].textContent = contador++; // renumerar
            } else {
                row.style.display = 'none';
            }
        });

        // Reasignar eventos después del filtro
        asignarEventosClick();
    });

    // Activar eventos al cargar
    asignarEventosClick();

    /*  MENÚ USUARIO  */

    const toggleUsuario = document.querySelector('.toggle-usuario');
    const submenu = document.getElementById('submenu-usuario');

    if(toggleUsuario && submenu){

        // Mostrar / ocultar menú
        toggleUsuario.addEventListener('click', () => {
            submenu.classList.toggle('active');
        });

        // Cerrar si se hace click fuera
        document.addEventListener('click', (e) => {
            if (!toggleUsuario.contains(e.target) && !submenu.contains(e.target)) {
                submenu.classList.remove('active');
            }
        });
    }

});