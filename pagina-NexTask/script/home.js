document.addEventListener("DOMContentLoaded", () => {

    /*  ELEMENTOS DEL DOM  */

    // Modales principales
    const modal = document.getElementById('modal-form');
    const modalEstado = document.getElementById('modal-estado');
    const modalEliminar = document.getElementById("modal-eliminar");

    // Botones principales
    const btnAgregar = document.getElementById('btn-agregar');
    const btnCancelar = document.getElementById('btn-cancelar');
    const btnEliminar = document.getElementById("btn-eliminar");

    // Botones para cerrar modales
    const cerrarForm = document.getElementById('cerrar-form');
    const cerrarEstado = document.getElementById('cerrar-estado');

    // Formulario y campos
    const form = document.getElementById('form-tarea');
    const selectMateria = form.materia;
    const materiasOriginales = selectMateria.innerHTML;
    const inputFecha = form.fecha_limite;

    // Fecha de hoy
    const hoy = new Date();

    // Formato YYYY-MM-DD
    const formatoFecha = (fecha) => {
        const year = fecha.getFullYear();
        const month = String(fecha.getMonth() + 1).padStart(2, '0');
        const day = String(fecha.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // Mínimo = hoy
    inputFecha.min = formatoFecha(hoy);

    // Máximo = hoy + 1 año
    const unAnio = new Date();
    unAnio.setFullYear(hoy.getFullYear() + 1);
    inputFecha.max = formatoFecha(unAnio);

    const nombre = document.getElementById('nombre-tarea');
    const detalle = document.getElementById('detalle-tarea');
    const botonesEstado = document.querySelectorAll('.estado-btn');
    const editar = document.getElementById('editar-tarea-modal');

    // Slider de dificultad
    const range = document.getElementById('range-dif');
    const valor = document.getElementById('valor-dificultad');

    // Menú usuario
    const usuarioMenu = document.getElementById("usuario-menu");
    const submenu = document.getElementById("submenu-usuario");


    /*  MENÚ DE USUARIO  */
    if (usuarioMenu && submenu) {
        usuarioMenu.addEventListener("click", (e) => {
            e.stopPropagation();
            submenu.classList.toggle("active");
        });

        // Cierra el menú si se hace click fuera
        document.addEventListener("click", () => {
            submenu.classList.remove("active");
        });
    }


    /*  VARIABLES DE CONTROL  */
    let tarea = null;              // tarea seleccionada
    let tareaAEliminar = null;    // id de tarea a eliminar


    /*  TOAST DESDE URL  */
    const params = new URLSearchParams(window.location.search);
    let huboMensaje = false;

    if (params.get("msg") === "guardado") {
        mostrarToast("✅ Tarea guardada correctamente");
        huboMensaje = true;
    }

    if (params.get("msg") === "estado") {
        mostrarToast("🔄 Estado cambiado correctamente");
        huboMensaje = true;
    }

    if (params.get("msg") === "editado") {
        mostrarToast("✏️ Tarea editada correctamente");
        huboMensaje = true;
    }

    if (params.get("msg") === "eliminado") {
        mostrarToast("🗑 Tarea eliminada");
        huboMensaje = true;
    }

    // Limpia la URL después de mostrar el mensaje
    if (huboMensaje) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }


    /*  ABRIR MODAL (NUEVA TAREA)  */
    btnAgregar.onclick = () => {

        // Reinicia formulario
        form.reset();
        form.querySelector('[name="id"]').value = "";

        // Restaura lista de materias original
        selectMateria.innerHTML = materiasOriginales;

        // Ajustes visuales
        document.getElementById("titulo-form").textContent = "Agregar nueva tarea";
        btnEliminar.style.display = "none";

        // Reset dificultad
        range.value = 1;
        valor.textContent = "1";
        range.dataset.movido = "false";

        modal.classList.add('active');
    };


    /*  CERRAR MODALES  */
    btnCancelar.onclick = cerrarForm.onclick = () => modal.classList.remove('active');
    cerrarEstado.onclick = () => modalEstado.classList.remove('active');

    // Cerrar si se hace click fuera del modal
    window.onclick = (e) => {
        if (e.target === modal) modal.classList.remove('active');
        if (e.target === modalEstado) modalEstado.classList.remove('active');
        if (e.target === modalEliminar) modalEliminar.classList.remove('active');
    };


    /*  VER DETALLE DE TAREA  */
    document.addEventListener('click', (e) => {
        const c = e.target.closest('.card');
        if (!c) return;

        tarea = c;

        nombre.textContent = c.dataset.materia;
        detalle.textContent = c.dataset.detalles || "Sin detalles";

        // Deshabilita estados inválidos
        botonesEstado.forEach(b => {

            // Si está terminada → ocultar TODOS los botones
            if (c.dataset.estado === "terminadas") {
                b.style.display = "none";
                return;
            }

            // Si NO está terminada → mostrar botones
            b.style.display = "block";

            // Reset
            b.disabled = false;

            // No permitir seleccionar el mismo estado
            if (b.dataset.estado === c.dataset.estado) {
                b.disabled = true;
            }

            // Bloquear regreso de en proceso → asignadas
            if (c.dataset.estado === "enproceso" && b.dataset.estado === "asignadas") {
                b.disabled = true;
            }

        });

        // Bloquear edición si está terminada (SI SE QUIERE DESACTIVAR EDITAR EN "TERMINADAS")
        // editar.disabled = (c.dataset.estado === "terminadas");

        modalEstado.classList.add('active');
    });


    /*  CAMBIAR ESTADO  */
    botonesEstado.forEach(b => {
        b.onclick = () => {
            if (!tarea) return;

            fetch('./recursos/actualizar_estado.php', {
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`id=${tarea.dataset.id}&estado=${b.dataset.estado}`
            }).then(()=>{
                window.location.href = "home.php?msg=estado";
            });
        };
    });


    /*  EDITAR TAREA  */
    editar.onclick = () => {

        if (!tarea) return;

        modalEstado.classList.remove('active');
        document.getElementById("titulo-form").textContent = "Editando tarea";

        form.querySelector('[name="id"]').value = tarea.dataset.id;

        // Verificar si la materia existe en el select
        let existe = false;
        for (let option of form.materia.options) {
            if (option.value == tarea.dataset.materiaId) {
                existe = true;
                break;
            }
        }

        // Si la materia fue eliminada, la agrega temporalmente
        if (!existe) {
            let opt = document.createElement("option");
            opt.value = tarea.dataset.materiaId;
            opt.textContent = tarea.dataset.materia + " (eliminada)";
            opt.selected = true;
            form.materia.appendChild(opt);
        }

        form.materia.value = tarea.dataset.materiaId;

        // Cargar dificultad
        range.value = tarea.dataset.dificultad;
        valor.textContent = tarea.dataset.dificultad;
        range.dataset.movido = "true";

        // Separar fecha y hora
        const partes = tarea.dataset.fecha.split(" ");
        form.fecha_limite.value = partes[0];
        form.hora_limite.value = partes[1]?.substring(0,5) || "";

        form.detalles.value = tarea.dataset.detalles;
        form.estado.value = tarea.dataset.estado;

        btnEliminar.style.display = "inline-block";

        // Preparar eliminación
        btnEliminar.onclick = () => {
            tareaAEliminar = tarea.dataset.id;
            document.getElementById("nombre-tarea-eliminar").textContent = tarea.dataset.materia;
            modalEliminar.classList.add("active");
        };

        modal.classList.add('active');
    };


    /*  SLIDER DIFICULTAD  */
    range.oninput = () => {
        valor.textContent = range.value;
        range.dataset.movido = "true";
    };


    /*  VALIDACIÓN FORM  */
    form.addEventListener('submit', (e) => {
        if (range.dataset.movido !== "true") {
            e.preventDefault();
            mostrarToast("⚠️ Selecciona la dificultad");
        }
    });


    /*  ELIMINAR TAREA  */
    window.confirmarEliminar = function () {

        if (!tareaAEliminar) return;

        fetch("./recursos/eliminar_tarea.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "tarea_id=" + tareaAEliminar
        })
        .then(() => {
            window.location.href = "home.php?msg=eliminado";
        });
    };

    window.cerrarModalEliminar = function () {
        modalEliminar.classList.remove("active");
    };

});


/*  TOAST GLOBAL  */
function mostrarToast(mensaje) {

    const toastKanban = document.getElementById("toast-kanban");
    if (!toastKanban) return;

    toastKanban.textContent = mensaje;
    toastKanban.classList.add("show");

    setTimeout(() => {
        toastKanban.classList.remove("show");
    }, 3000);
}