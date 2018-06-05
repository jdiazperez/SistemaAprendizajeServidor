var httpRequest;
var usuarios;
var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));

function getUsuarios() {
    httpRequest = new XMLHttpRequest();
    httpRequest.open("GET", "/api/t/usuarios", true);
    httpRequest.responseType = "json";
    httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
    httpRequest.onload = comprobarCodGetUsuarios;
    httpRequest.send();
}

function comprobarCodGetUsuarios() {
    if (httpRequest.status === 200) {
        usuarios = httpRequest.response;
        listarUsuarios();
    } else {
        document.querySelector("#alertError").classList.remove("oculto");
    }
}

function listarUsuarios() {
    var containerUsuarios = document.querySelector("#containerUsuarios");

    for (var i = 1; i <= usuarios.length; i++) {
        var rol;
        if (usuarios[i - 1].maestro) {
            rol = "Maestro";
        } else {
            rol = "Aprendiz";
        }
        var rowUsuario = document.createElement("div");
        rowUsuario.id = "usuario" + i;
        rowUsuario.className = "row bg-light py-2 align-items-center usuario";
        rowUsuario.innerHTML =
            "<div class='col-sm'>" +
            "<a href='maestroEditarUsuario.html' class='h6 text-primary ml-2' onclick='editarUsuario(" + i + ")'>" +
            usuarios[i - 1].nombreUsuario + " (" + rol + ")" +
            "</a>" +
            "</div>" +
            "<div class='col-sm-3'>" +
            "<a class='btn btn-warning btn-sm mr-2' href='maestroEditarUsuario.html' onclick='editarUsuario(" + i + ")'>" +
            "<i class='fas fa-edit mr-1'></i>Editar/Permisos" +
            "</a>" +
            "<button class='btn btn-danger btn-sm' onclick='mostrarModal(" + i + ")'>" +
            "<i class='fas fa-trash-alt fa-sm mr-1'></i>Eliminar" +
            "</button>" +
            "</div>";
        containerUsuarios.appendChild(rowUsuario);
    }
}

function editarUsuario(idUsuario) {
    localStorage.setItem("editarUsuario", JSON.stringify(usuarios[idUsuario - 1]));
}

function mostrarModal(idUsuario) {
    document.querySelector(".modal-body").textContent = "¿Estás seguro de que deseas eliminar el usuario?: " + usuarios[idUsuario - 1].nombreUsuario;
    document.querySelector("#btnEliminarUsuario").addEventListener("click", function () {
        eliminarUsuario(idUsuario);
    });
    $('#modalEliminarUsuario').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function eliminarUsuario(idUsuario) {
    httpRequest = new XMLHttpRequest();
    httpRequest.open("DELETE", "/api/t/usuarios/" + usuarios[idUsuario - 1].id, true);
    httpRequest.responseType = "json";
    httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
    httpRequest.onload = comprobarCodEliminarUsuario;
    httpRequest.send();
}

function comprobarCodEliminarUsuario() {
    if (httpRequest.status === 204) {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Usuario eliminado correctamente';
    } else {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-exclamation-triangle text-danger h2 mr-2"></i> Error en el servidor. Inténtelo de nuevo más tarde.';
    }
    document.querySelector(".modal-footer").innerHTML = '<a class="btn btn-secondary" href="/maestroGestionUsuarios">Cerrar</a>';

}
