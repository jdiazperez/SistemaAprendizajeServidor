var datos;

function listarCuestiones(){
    
}

function listarCuestiones2() {
    datos = JSON.parse(localStorage.getItem("datos"));
    var containerCuestiones = document.querySelector("#containerCuestiones");

    for (var i = 1; i <= datos.cuestiones.length; i++) {
        var rowCuestion = document.createElement("div");
        rowCuestion.id = "cuestion" + i;
        rowCuestion.className = "row bg-light py-2 align-items-center cuestion";
        rowCuestion.innerHTML =
            "<div class='col-sm'>" +
            "<a href='editarCuestionMaestro.html' class='h6 text-primary ml-2' onclick='editarCuestion(" + i + ")'>" + 
            datos.cuestiones[i-1].enunciado + 
            "</a>" +
            "</div>" +
            "<div class='col-sm-2'>" +
            "<button class='btn btn-danger btn-sm' onclick='eliminarCuestion(" + i + ")'>" +
            "<i class='fas fa-trash-alt fa-sm mr-1'></i>Eliminar" +
            "</button>" +
            "</div>";
        containerCuestiones.appendChild(rowCuestion);
    }
}

function editarCuestion(idCuestion) {
    localStorage.setItem("editarCuestion", idCuestion);
}

function eliminarCuestion(idCuestion) {
    datos.cuestiones.splice(idCuestion - 1, 1);
    localStorage.setItem("datos", JSON.stringify(datos));
    location.reload();
}

function nuevaCuestion() {
    localStorage.setItem("editarCuestion", datos.cuestiones.length + 1);
}
