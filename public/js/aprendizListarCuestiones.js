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
    var cuestion;

    for (var i = 1; i <= cuestiones.length; i++) {
        cuestion = cuestiones[i - 1];

        if (cuestion.disponible) {
            var rowCuestion = document.createElement("div");
            rowCuestion.id = "cuestion" + i;
            rowCuestion.className = "row bg-light py-2 align-items-center cuestion";
            rowCuestion.innerHTML =
                "<div class='col-sm'>" +
                "<a href='aprendizResponderCuestion.html' id='cuestion" + i + "'" + "class='h6 text-primary ml-2' onclick='responderCuestion(" + i + ")'>" +
                cuestion.enunciado +
                "</a>" +
                "</div>";
            containerCuestiones.appendChild(rowCuestion);
        }
    }
}

function responderCuestion(idCuestion) {
    localStorage.setItem("responderCuestion", JSON.stringify(cuestiones[idCuestion - 1]));
}
