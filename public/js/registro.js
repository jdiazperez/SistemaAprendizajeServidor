var httpRequest;

var alertDisponible = document.querySelector("#alertDisponible");
var alertNoDisponible = document.querySelector("#alertNoDisponible");
var textNombreUsuario = document.querySelector("#nombreUsuario");
var btnComprobarDisponibilidad = document.querySelector("#btnComprobarDisponibilidad");
var btnCambiarUsuario = document.querySelector("#btnCambiarUsuario");
var divDatosUsuario = document.querySelector("#datosUsuario");

function comprobarDisponibilidadUsuario() {
    var nombreUsuario = textNombreUsuario.value;
    httpRequest = new XMLHttpRequest();
    httpRequest.open("GET", "/api/users/nombreUsuario/" + nombreUsuario, true);
    httpRequest.responseType = "json";
    httpRequest.onload = comprobarCodDisponibilidad;
    httpRequest.send();
}

function comprobarCodDisponibilidad() {
    if (httpRequest.status === 200) {
        alertNoDisponible.classList.remove("oculto");

    } else {
        textNombreUsuario.disabled = true;
        alertDisponible.classList.remove("oculto");
        alertNoDisponible.classList.add("oculto");
        btnComprobarDisponibilidad.classList.add("oculto");
        btnCambiarUsuario.classList.remove("oculto");
        divDatosUsuario.classList.remove("oculto");
    }
}

function cambiarNombreUsuario() {
    textNombreUsuario.disabled = false;
    alertDisponible.classList.add("oculto");
    alertNoDisponible.classList.add("oculto");
    btnComprobarDisponibilidad.classList.remove("oculto");
    btnCambiarUsuario.classList.add("oculto");
    divDatosUsuario.classList.add("oculto");
}

function registro() {
    var contrasenia = document.querySelector("#contrasenia").value;
    var repetirContrasenia = document.querySelector("#contrasenia2").value;
    if (contrasenia !== repetirContrasenia) {
        document.querySelector("#alertContrasenias").classList.remove("oculto");

    } else {
        textNombreUsuario.disabled = false;
        var form = document.querySelector("#formRegistro");
        var formData = new FormData(form);
        httpRequest = new XMLHttpRequest();
        httpRequest.open("POST", "/api/registro", true);
        httpRequest.responseType = "json";
        httpRequest.onload = comprobarCodRegistro;
        httpRequest.send(formData);
    }
    return false;
}

function comprobarCodRegistro() {
    if (httpRequest.status === 201) {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Usuario creado correctamente. Próximamente, un maestro le autorizará el acceso.';

    } else {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-exclamation-triangle text-danger h2 mr-2"></i> Error en el servidor. Inténtelo de nuevo más tarde.';
    }
    $('#modalRegistro').modal({backdrop: 'static', keyboard: false});
}
