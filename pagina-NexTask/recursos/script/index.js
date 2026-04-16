// Botones para cambiar entre vistas
const botonRegistro = document.getElementById("boton-registro");
const botonLogin = document.getElementById("boton-login");

// Contenedor principal animado
const panelAnimado = document.getElementById("panel-animado");

// Activa panel de registro
botonRegistro.addEventListener("click", () => {
    panelAnimado.classList.add("activo");
});

// Regresa a login
botonLogin.addEventListener("click", () => {
    panelAnimado.classList.remove("activo");
});

// Contenedor del toast
const toast = document.getElementById("toast");

// Función para mostrar mensajes
function mostrarToast(mensaje) {
    toast.textContent = mensaje;
    toast.classList.add("show");

    // Oculta después de 3 segundos
    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}

// Captura parámetros tipo ?error=pass
const params = new URLSearchParams(window.location.search);
const error = params.get("error");
const recuperar = params.get("recuperar");
const pass = params.get("pass");
const registro = params.get("registro");

// Detecta qué panel debe mostrarse
const panel = params.get("panel");

// Si viene registro → activa ese panel
if (panel === "registro") {
    panelAnimado.classList.add("activo");
}

// Mensajes del login
if (error === "pass") {
    mostrarToast("❌ Contraseña incorrecta !!! ");
}
// Mensaje de registro exitoso
if (registro === "ok") {
    mostrarToast("✅ Registrado correctamente. Inicia sesión");

    // Asegura que se muestre el login
    panelAnimado.classList.remove("activo");
}

if (error === "user") {
    mostrarToast("❌ No se encontró el usuario !!! ");
}

if (error === "confirm") {
    mostrarToast("❌ Las contraseñas no coinciden !!! ");
}

// Mensajes para recuperar contraseña
if (recuperar === "ok") {
    mostrarToast("✅ Datos correctos. Tu contraseña es: " + pass);
}

if (recuperar === "error") {
    mostrarToast("❌ Datos incorrectos !!! ");
}

// Limpiar URL (para que no se repita el mensaje)
if (error || recuperar || registro) {
    window.history.replaceState({}, document.title, window.location.pathname);
}

// Abre el modal
function abrirModalRecuperar() {
    document.getElementById("modal-recuperar").classList.add("active");
}

// Cierra el modal
function cerrarModalRecuperar() {
    document.getElementById("modal-recuperar").classList.remove("active");
}

// Cerrar al hacer clic fuera del contenido
const modal = document.getElementById("modal-recuperar");

modal.addEventListener("click", function (e) {
    if (e.target === this) {
        cerrarModalRecuperar();
    }
});

// Funcion para ver contraseña
function togglePassword(idInput, elemento) {
    const input = document.getElementById(idInput);

    if (input.type === "password") {
        input.type = "text";
        elemento.textContent = "Ocultar";
    } else {
        input.type = "password";
        elemento.textContent = "Ver";
    }
}

// Para que funcionen desde el HTML (onclick)
window.abrirModalRecuperar = abrirModalRecuperar;
window.cerrarModalRecuperar = cerrarModalRecuperar;