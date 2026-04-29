document.addEventListener("DOMContentLoaded", () => {

    const botonRegistro = document.getElementById("boton-registro");
    const botonLogin = document.getElementById("boton-login");
    const panelAnimado = document.getElementById("panel-animado");
    const toast = document.getElementById("toast");

    // Activa panel de registro
    if (botonRegistro) {
        botonRegistro.addEventListener("click", () => {
            panelAnimado.classList.add("activo");
        });
    }//if

    // Regresa a login
    if (botonLogin) {
        botonLogin.addEventListener("click", () => {
            panelAnimado.classList.remove("activo");
        });
    }//if

    function mostrarToast(mensaje) {
        toast.textContent = mensaje;
        toast.classList.add("show");

        // Oculta después de 3 segundos
        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000);
    }//mostrarToast

    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");
    const recuperar = params.get("recuperar");
    const pass = params.get("pass");
    const registro = params.get("registro");
    const panel = params.get("panel");

    let huboMensaje = false;

    if (panel === "registro" || error === "duplicado" || error === "confirm") {
        setTimeout(() => {
            panelAnimado.classList.add("activo");
        }, 50);
    }//if

    if (error === "pass") {
        mostrarToast("❌ Contraseña incorrecta !!!");
        huboMensaje = true;
    }//if
    if (error === "user") {
        mostrarToast("❌ No se encontró el usuario !!!");
        huboMensaje = true;
    }//if
    if (error === "confirm") {
        mostrarToast("❌ Las contraseñas no coinciden !!!");
        huboMensaje = true;
    }//if
    if (error === "duplicado") {
        mostrarToast("❌ El nombre de usuario o correo ya están registrados.");
        huboMensaje = true;
    }//if
    if (registro === "ok") {
        mostrarToast("✅ Registrado correctamente. Inicia sesión");
        panelAnimado.classList.remove("activo");
        huboMensaje = true;
    }//if
    if (recuperar === "ok") {
        mostrarToast("✅ Datos correctos. Tu contraseña es: " + pass);
        huboMensaje = true;
    }//if
    if (recuperar === "error") {
        mostrarToast("❌ Datos incorrectos !!!");
        huboMensaje = true;
    }//if

    if (huboMensaje || panel) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }//if

    window.abrirModalRecuperar = function() {
        document.getElementById("modal-recuperar").classList.add("active");
    };

    window.cerrarModalRecuperar = function() {
        document.getElementById("modal-recuperar").classList.remove("active");
    };

    const modalRecuperar = document.getElementById("modal-recuperar");
    if (modalRecuperar) {
        modalRecuperar.addEventListener("click", function (e) {
            if (e.target === this) {
                window.cerrarModalRecuperar();
            }//if
        });
    }//if

    window.togglePassword = function(idInput, elemento) {
        const input = document.getElementById(idInput);
        if (input.type === "password") {
            input.type = "text";
            elemento.textContent = "Ocultar";
        }//if
         else {
            input.type = "password";
            elemento.textContent = "Ver";
        }//else
    };

});