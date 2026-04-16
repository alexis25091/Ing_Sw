// Referencias a modales
const modalUser = document.getElementById("modal-user");
const modalMateria = document.getElementById("modal-materia");

const rangeMateria = document.getElementById("range-dif-materia");
const valorMateria = document.getElementById("valor-dificultad-materia");

rangeMateria.dataset.movido = "false";

/* PERFIL */

// Abrir modal de usuario
document.getElementById("btn-editar").onclick = () => {
    modalUser.classList.add("active");
};

// Cerrar modal de usuario
function cerrarModalUser(){
    modalUser.classList.remove("active");
}

/* MATERIAS */

// Slider dinamico dificultad
rangeMateria.oninput = () => {
    valorMateria.textContent = rangeMateria.value;
    rangeMateria.dataset.movido = "true"; // 👈 clave
};

// Abrir modal para nueva materia
document.getElementById("btn-agregar").onclick = () => {
    document.getElementById("materia-id").value = "";
    document.getElementById("materia-nombre").value = "";
    document.getElementById("materia-color").value = "";

    // Quitar selección de colores
    document.querySelectorAll(".color-option").forEach(c => c.classList.remove("selected"));

    modalMateria.classList.add("active");

    rangeMateria.value = 1;
    valorMateria.textContent = "1";
    rangeMateria.dataset.movido = "false"; // 👈 IMPORTANTE
};

// Cerrar modal de materia
function cerrarModalMateria(){
    modalMateria.classList.remove("active");
}

// Cargar datos para editar materia
function editarMateria(id,nombre,color,dificultad){
    modalMateria.classList.add("active");

    document.getElementById("materia-id").value = id;
    document.getElementById("materia-nombre").value = nombre;
    document.getElementById("materia-color").value = color;
    rangeMateria.value = dificultad;
    valorMateria.textContent = dificultad;
    rangeMateria.dataset.movido = "true";

    // Marcar color seleccionado
    document.querySelectorAll(".color-option").forEach(c=>{
        c.classList.remove("selected");
        if(c.dataset.color === color){
            c.classList.add("selected");
        }
    });
}


// Selección de color
document.querySelectorAll(".color-option").forEach(el => {
    el.addEventListener("click", () => {

        document.querySelectorAll(".color-option").forEach(c => c.classList.remove("selected"));

        el.classList.add("selected");

        document.getElementById("materia-color").value = el.dataset.color;
    });
});

// Validar formulario antes de enviar
document.querySelector("#modal-materia form").addEventListener("submit", function(e){

    const nombre = document.getElementById("materia-nombre").value.trim();
    const color = document.getElementById("materia-color").value;
    const dificultad = rangeMateria.value;

    if(rangeMateria.dataset.movido !== "true"){
        e.preventDefault();
        mostrarToast("⚠️ Selecciona la dificultad");
        return;
    }

    if(nombre === ""){
        e.preventDefault();
        mostrarToast("⚠️ La materia necesita un nombre");
        return;
    }

    if(color === ""){
        e.preventDefault();
        mostrarToast("🎨 Selecciona un color para la materia");
        return;
    }
});

/* TOAST */

// Mostrar notificación
const toast = document.getElementById("toast");

function mostrarToast(mensaje) {
    toast.textContent = mensaje;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}

// Leer parámetros de la URL
const params = new URLSearchParams(window.location.search);

// Mensajes de usuario
if(params.get("user") === "nombre_ok"){
    mostrarToast("‼️ Se actualizó el nombre");
}
if(params.get("user") === "email_ok"){
    mostrarToast("‼️ Se actualizó el correo");
}
if(params.get("user") === "pass_ok"){
    mostrarToast("‼️ Se actualizó la contraseña correctamente");
}
if(params.get("error") === "pass"){
    mostrarToast("❌ Contraseña actual es incorrecta");
}

// Mensajes de materias
if(params.get("materia") === "add_ok"){
    mostrarToast("✅ Se agregó correctamente la materia");
}
if(params.get("materia") === "edit_ok"){
    mostrarToast("✏️ Se editó correctamente la materia");
}
if(params.get("materia") === "delete_ok"){
    mostrarToast("🗑 Se eliminó correctamente");
}

if(params.get("error") === "misma_pass"){
    mostrarToast("⚠️ La nueva contraseña no puede ser igual a la actual");
}

// Limpiar parámetros de la URL
if (window.location.search) {
    window.history.replaceState({}, document.title, window.location.pathname);
}

let materiaAEliminar = null;

// Abrir modal de eliminar materia
function eliminarMateria(id, nombre){
    console.log("Intentando abrir modal de eliminar", id, nombre);
    materiaAEliminar = id;
    document.getElementById("nombre-materia-eliminar").textContent = nombre;
    document.getElementById("materia-id-eliminar").value = id;
    document.getElementById("modal-eliminar-materia").classList.add("active");
}

// Confirmar eliminación
function confirmarEliminarMateria(){
    if(!materiaAEliminar) return;

    // Enviamos POST al PHP para eliminar la materia
    fetch("./recursos/materias.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "delete=" + materiaAEliminar
    }).then(() => {
        // Recargar la página con mensaje
        window.location.href = "perfil.php?materia=delete_ok";
    });
}

// Cerrar modal eliminar
function cerrarModalEliminarMateria(){
    document.getElementById("modal-eliminar-materia").classList.remove("active");
    materiaAEliminar = null;
}

// Funcion para ver contraseña
function togglePass(id, el){
    const input = document.getElementById(id);

    if(input.type === "password"){
        input.type = "text";
        el.textContent = "Ocultar";
    } else {
        input.type = "password";
        el.textContent = "Ver";
    }
}