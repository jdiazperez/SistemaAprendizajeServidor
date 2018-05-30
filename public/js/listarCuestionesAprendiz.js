var datos;

function listarCuestiones() {
    datos = JSON.parse(localStorage.getItem("datos"));
    var containerCuestiones = document.querySelector("#containerCuestiones");
    var cuestion;

    for (var i = 1; i <= datos.cuestiones.length; i++) {
        cuestion = datos.cuestiones[i - 1];

        if (cuestion.disponible) {
            var rowCuestion = document.createElement("div");
            rowCuestion.id = "cuestion" + i;
            rowCuestion.className = "row bg-light py-2 align-items-center cuestion";
            rowCuestion.innerHTML =
                "<div class='col-sm'>" +
                "<a href='responderCuestionAprendiz.html' id='cuestion" + i + "'" + "class='h6 text-primary ml-2' onclick='responderCuestion(" + i + ")'>" +
                datos.cuestiones[i - 1].enunciado +
                "</a>" +
                "</div>";
            containerCuestiones.appendChild(rowCuestion);
        }
    }
}

function responderCuestion(idCuestion) {
    localStorage.setItem("responderCuestion", idCuestion);
}
