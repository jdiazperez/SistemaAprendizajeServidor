var httpRequest;
var cuestiones;
var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));

function getCuestiones() {
    httpRequest = new XMLHttpRequest();
    httpRequest.open("GET", "/api/t/cuestiones", true);
    httpRequest.responseType = "json";
    httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
    httpRequest.onload = comprobarCodGetCuestiones;
    httpRequest.send();
}

function comprobarCodGetCuestiones() {
    if (httpRequest.status === 200) {
        cuestiones = httpRequest.response;
        listarCuestiones();
    } else {
        document.querySelector("#alertError").classList.remove("oculto");
    }
}

function listarCuestiones() {
    var containerCuestiones = document.querySelector("#containerCuestiones");

    for (var i = 1; i <= cuestiones.length; i++) {
        var rowCuestion = document.createElement("div");
        rowCuestion.id = "cuestion" + i;
        rowCuestion.className = "row bg-light py-2 align-items-center cuestion";
        rowCuestion.innerHTML =
            "<div class='col-sm'>" +
            "<a href='maestroEditarCuestion.html' class='h6 text-primary ml-2' onclick='editarCuestion(" + i + ")'>" +
            cuestiones[i - 1].enunciado +
            "</a>" +
            "</div>" +
            "<div class='col-sm-3'>" +
            "<a class='btn btn-warning btn-sm mr-2' href='maestroEditarCuestion.html' onclick='editarCuestion(" + i + ")'>" +
            "<i class='fas fa-edit mr-1'></i>Editar/Corregir" +
            "</a>" +
            "<button class='btn btn-danger btn-sm' onclick='mostrarModal(" + i + ")'>" +
            "<i class='fas fa-trash-alt fa-sm mr-1'></i>Eliminar" +
            "</button>" +
            "</div>";
        containerCuestiones.appendChild(rowCuestion);
    }
}

function editarCuestion(idCuestion) {
    localStorage.setItem("editarCuestion", JSON.stringify(cuestiones[idCuestion - 1]));
}

function mostrarModal(idCuestion) {
    document.querySelector(".modal-body").textContent = "¿Estás seguro de que deseas eliminar la cuestion?: " + cuestiones[idCuestion - 1].enunciado;
    document.querySelector("#btnEliminarCuestion").addEventListener("click", function () {
        eliminarCuestion(idCuestion);
    });
    $('#modalEliminarCuestion').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function eliminarCuestion(idCuestion) {
    httpRequest = new XMLHttpRequest();
    httpRequest.open("DELETE", "/api/t/cuestiones/" + cuestiones[idCuestion - 1].id, true);
    httpRequest.responseType = "json";
    httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
    httpRequest.onload = comprobarCodEliminarCuestion;
    httpRequest.send();
}

function comprobarCodEliminarCuestion() {
    if (httpRequest.readyState === 4) {
        if (httpRequest.status === 204) {
            document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Cuestión eliminada correctamente';
        } else {
            document.querySelector(".modal-body").innerHTML = '<i class="fas fa-exclamation-triangle text-danger h2 mr-2"></i> Error en el servidor. Inténtelo de nuevo más tarde.';
        }
        document.querySelector(".modal-footer").innerHTML = '<a class="btn btn-secondary" href="/maestroGestionCuestiones">Cerrar</a>';
    }
}

function nuevaCuestion() {
    localStorage.setItem("editarCuestion", null);
}
