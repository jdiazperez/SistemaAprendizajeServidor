var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));
var idCuestion;
var cuestion;
var soluciones = [];
var numSoluciones = 0;
var razonamientos = [];
var numRazonamientos = [];
var sectionCuestion;

function mostrarCuestion() {
    cuestion = JSON.parse(localStorage.getItem("editarCuestion"));
    sectionCuestion = document.querySelector("#cuestion");
    var containerEnunciado = document.createElement("div");
    containerEnunciado.className = "container bg-light border p-4 mt-5";
    containerEnunciado.innerHTML =
        "<div class='form-group'>" +
        "<label for='enunciado'><h4 class='text-primary'>Enunciado</h4></label>" +
        "<input type='text' id='enunciado' class='form-control' placeholder='Introducir la pregunta' required>" +
        "</div>" +
        "<div class='row'>" +
        "<div class='custom-control custom-checkbox ml-3'>" +
        "<input type='checkbox' class='custom-control-input' id='disponible' onclick='marcarDisponibleParaAprendices()'>" +
        "<label for='disponible' class='custom-control-label'>Disponible para los aprendices</label>" +
        "</div>" +
        "</div>" +
        "<div class='row mt-3 ml-1'>" +
        "<button class='btn btn-primary' type='button' onclick='nuevaSolucion()'><i class='fa fa-comment mr-2'></i>Nueva Solución</button>" +
        "</div>";
    sectionCuestion.appendChild(containerEnunciado);

    if (cuestion !== null) {
        idCuestion = cuestion.id;
        /*        numSoluciones = cuestion.soluciones.length;

                for (var i = 0; i < numSoluciones; i++) {
                    if (cuestion.soluciones[i].hasOwnProperty("razonamientos")) {
                        numRazonamientos[i] = cuestion.soluciones[i].razonamientos.length;
                    } else {
                        numRazonamientos[i] = 0;
                    }
                }*/
        getSoluciones();
        containerEnunciado.querySelector("#enunciado").value = cuestion.enunciado;
        if (cuestion.disponible) {
            containerEnunciado.querySelector("#disponible").checked = true;
        }
        //rellenarCampos(containerEnunciado);

    } else {
        alert("Nueva cuestion");
    }
}

function getSoluciones() {
    httpRequest = new XMLHttpRequest();
    httpRequest.open("GET", "/api/t/soluciones/" + idCuestion, true);
    httpRequest.responseType = "json";
    httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
    httpRequest.onload = comprobarCodGetSoluciones;
    httpRequest.send();
}

function comprobarCodGetSoluciones() {
    if (httpRequest.status === 200) {
        soluciones = httpRequest.response;
        numSoluciones = soluciones.length;
        var requestRazonamientos = [];
        for (var i = 0; i < soluciones.length; i++) {
            requestRazonamientos[i] = new XMLHttpRequest();
            getRazonamientos(requestRazonamientos[i], i);
            rellenarSolucion(soluciones[i], i + 1);
        }
    } else {
        document.querySelector("#alertError").classList.remove("oculto");
    }
}

function getRazonamientos(request, i) {
    request.open("GET", "/api/t/razonamientos/" + soluciones[i].id, true);
    request.responseType = "json";
    request.setRequestHeader("X-Token", usuarioIdentificado.jwt);
    request.onload = function () {
        comprobarCodGetRazonamientos(request, i);
    };
    request.send();
}

function comprobarCodGetRazonamientos(request, i) {
    if (request.status === 200) {
        razonamientos[i] = request.response;
        numRazonamientos[i] = razonamientos[i].length;
        for (var j = 0; j < razonamientos[i].length; j++) {
            rellenarRazonamiento(razonamientos[i][j], i + 1, j + 1);
        }
    } else {
        razonamientos[i] = [];
        document.querySelector("#alertError").classList.remove("oculto");
    }
}

function rellenarSolucion(solucion, i) {
    var containerSolucion = crearContainerSolucion(solucion.propuestaPorAlumno, solucion.respuesta, i);
    containerSolucion.setAttribute("data-id", solucion.id);
    sectionCuestion.appendChild(containerSolucion);

    if (solucion.propuestaPorAlumno) {
        var divOpcionesSolucion = document.createElement("div");
        divOpcionesSolucion.id = "opciones" + i;
        divOpcionesSolucion.innerHTML =
            '<div class="row mt-3 ml-1">' +
            '<button class="btn btn-success" type="button" onclick="corregirSolucion(' + i + ')"><i class="fas fa-check-circle mr-2"></i>Corregir Solución</button>' +
            '<button class="btn btn-danger ml-3" type="button" onclick="eliminarSolucion(' + i + ')"><i class="fas fa-trash-alt mr-2"></i>Eliminar Solución</button>' +
            '</div>';
        containerSolucion.appendChild(document.createElement("hr"));
        containerSolucion.appendChild(divOpcionesSolucion);

        crearDropItemNuevaPropuesta(true, i, undefined);

    } else {
        if (solucion.correcta) {
            containerSolucion.querySelector("#correcta" + i).checked = true;

            containerSolucion.appendChild(document.createElement("hr"));
            var divEliminarSolucion = crearDivEliminarSolucion(i);
            containerSolucion.appendChild(divEliminarSolucion);

        } else {
            containerSolucion.querySelector("#incorrecta" + i).checked = true;
            containerSolucion.appendChild(document.createElement("hr"));
            var divOpcionesSolucion = crearDivOpcionesSolucion(i);
            containerSolucion.appendChild(divOpcionesSolucion);
        }
    }
}

function crearContainerSolucion(propuestaPorAlumno, respuesta, i) {
    var containerSolucion;
    var claseContainerSolucion;
    var titulo;
    var claseTitulo;

    if (propuestaPorAlumno) {
        claseContainerSolucion = "container bg-light border border-info p-4 mt-4 propuestaPorAlumno";
        titulo = "¡¡¡Propuesta de Solución!!!";
        claseTitulo = "text-light bg-info";
    } else {
        claseContainerSolucion = "container bg-light border p-4 mt-4";
        titulo = "Solución " + i;
        claseTitulo = "text-primary";
    }
    containerSolucion = document.createElement("div");
    containerSolucion.id = "solucion" + i;
    containerSolucion.className = claseContainerSolucion;
    containerSolucion.innerHTML =
        '<div class="form-group">' +
        '<label for="textSolucion' + i + '"><h4 class="' + claseTitulo + '">' + titulo + '</h4></label>' +
        '<textarea id="textSolucion' + i + '" rows="3" class="form-control" placeholder="Introducir la respuesta" required>' +
        respuesta +
        '</textarea>' +
        '</div>' +
        '<div class="row">' +
        '<div class="custom-control custom-radio ml-3">' +
        '<input type="radio" id="correcta' + i + '" name="tipoSolucion' + i + '" class="custom-control-input" onclick="marcarSolucionCorrecta(' + i + ')" required>' +
        '<label for="correcta' + i + '" class="custom-control-label">Solución Correcta</label>' +
        '</div>' +
        '<div class="custom-control custom-radio ml-3">' +
        '<input type="radio" id="incorrecta' + i + '" name="tipoSolucion' + i + '" class="custom-control-input" onclick="marcarSolucionIncorrecta(' + i + ')">' +
        '<label for="incorrecta' + i + '" class="custom-control-label">Solución Incorrecta</label>' +
        '</div>' +
        '</div>';
    return containerSolucion;
}

function rellenarRazonamiento(razonamiento, i, j) {
    var containerSolucion = document.querySelector("#solucion" + i);
    var divOpciones = document.querySelector("#opciones" + i);
    var divRazonamiento = crearDivRazonamiento(razonamiento.propuestoPorAlumno, razonamiento.texto, i, j);
    divRazonamiento.setAttribute("data-id", razonamiento.id);

    if (razonamiento.propuestoPorAlumno) {
        var divOpcionesRazonamiento = document.createElement("div");
        divOpcionesRazonamiento.className = "row mt-3 ml-2";
        divOpcionesRazonamiento.innerHTML =
            '<button class="btn btn-success" type="button" onclick="corregirRazonamiento(' + i + ', ' + j + ')"><i class="fas fa-check-circle mr-2"></i>Corregir Razonamiento</button>' +
            '<button class="btn btn-warning ml-3" type="button" onclick="eliminarRazonamiento(' + i + ', ' + j + ')"><i class="fas fa-trash-alt mr-2"></i>Eliminar Razonamiento</button>';
        divRazonamiento.appendChild(divOpcionesRazonamiento);

        crearDropItemNuevaPropuesta(false, i, j);

    } else {
        if (razonamiento.justificado) {
            divRazonamiento.querySelector("#justificado" + j + "Solucion" + i).checked = true;

        } else {
            divRazonamiento.querySelector("#injustificado" + j + "Solucion" + i).checked = true;

            var divError = crearDivError(i, j, razonamiento.error);
            divRazonamiento.appendChild(divError);
        }
        var divEliminarRazonamiento = crearDivEliminarRazonamiento(i, j);
        divRazonamiento.appendChild(divEliminarRazonamiento);

    }
    containerSolucion.insertBefore(divRazonamiento, divOpciones);
    containerSolucion.insertBefore(document.createElement("hr"), divOpciones);

}

function crearDivRazonamiento(propuestoPorAlumno, textoRazonamiento, i, j) {
    var divRazonamiento;
    var claseDivRazonamiento;
    var titulo;
    var claseTitulo;

    if (propuestoPorAlumno) {
        claseDivRazonamiento = "border border-info propuestoPorAlumno mt-5 ml-2";
        titulo = "¡¡¡Propuesta de Razonamiento!!!";
        claseTitulo = "text-light bg-info";

    } else {
        claseDivRazonamiento = "mt-5 ml-2";
        titulo = "Razonamiento " + j;
        claseTitulo = "text-info";
    }

    divRazonamiento = document.createElement("div");
    divRazonamiento.id = "razonamiento" + j + "Solucion" + i;
    divRazonamiento.className = claseDivRazonamiento;
    divRazonamiento.innerHTML =
        '<div class="form-group">' +
        '<label for="textoRazonamiento' + j + 'Solucion' + i + '"><h5 class="' + claseTitulo + '">' + titulo + '</h5></label>' +
        '<textarea id="textoRazonamiento' + j + 'Solucion' + i + '" rows="3" class="form-control" placeholder="Introducir el razonamiento a la respuesta" required>' +
        textoRazonamiento +
        '</textarea>' +
        '</div>' +
        '<div class="row">' +
        '<div class="custom-control custom-radio ml-4">' +
        '<input type="radio" id="justificado' + j + 'Solucion' + i + '" name="tipoRazonamiento' + j + 'Solucion' + i + '" class="custom-control-input" onclick="marcarRazonamientoJustificado(' + i + ', ' + j + ')" required>' +
        '<label for="justificado' + j + 'Solucion' + i + '" class="custom-control-label">Razonamiento Justificado</label>' +
        '</div>' +
        '<div class="custom-control custom-radio ml-3">' +
        '<input type="radio" id="injustificado' + j + 'Solucion' + i + '" name="tipoRazonamiento' + j + 'Solucion' + i + '" class="custom-control-input" onclick="marcarRazonamientoInjustificado(' + i + ', ' + j + ')">' +
        '<label for="injustificado' + j + 'Solucion' + i + '" class="custom-control-label">Razonamiento Injustificado</label>' +
        '</div>' +
        '</div>' +
        '</div>';

    return divRazonamiento;
}

function crearDivEliminarSolucion(i) {
    var divEliminarSolucion = document.createElement("div");
    divEliminarSolucion.id = "opciones" + i;
    divEliminarSolucion.innerHTML =
        '<div class="row mt-3 ml-1">' +
        '<button class="btn btn-danger" type="button" onclick="eliminarSolucion(' + i + ')"><i class="fas fa-trash-alt mr-2"></i>Eliminar Solución</button>' +
        '</div>';
    return divEliminarSolucion;
}

function crearDivOpcionesSolucion(i) {
    var divOpcionesSolucion = document.createElement("div");
    divOpcionesSolucion.id = "opciones" + i;
    divOpcionesSolucion.innerHTML =
        '<div class="row mt-3 ml-1">' +
        '<button class="btn btn-info" onclick="añadirRazonamiento(' + i + ')"><i class="far fa-comment-alt mr-2"></i>Añadir Razonamiento</button>' +
        '<button class="btn btn-danger ml-3" type="button" onclick="eliminarSolucion(' + i + ')"><i class="fas fa-trash-alt mr-2"></i>Eliminar Solución</button>' +
        '</div>';
    return divOpcionesSolucion;
}

function crearDivError(i, j, textoError) {
    var divError = document.createElement("div");
    divError.id = "error" + j + "Solucion" + i;
    divError.innerHTML =
        '<div class="form-group mt-3 ml-2">' +
        '<label for="textoError' + j + 'Solucion' + i + '"><h6 class="text-danger">Error</h6></label>' +
        '<textarea id="textoError' + j + 'Solucion' + i + '" rows="3" class="form-control" placeholder="Introducir el error del razonamiento" required>' +
        textoError +
        '</textarea>' +
        '</div>';
    return divError
}

function crearDivEliminarRazonamiento(i, j) {
    var divEliminarRazonamiento = document.createElement("div");
    divEliminarRazonamiento.id = "eliminar" + j + "Solucion" + i;
    divEliminarRazonamiento.innerHTML =
        '<div class="row mt-3 ml-2">' +
        '<button class="btn btn-warning" type="button" onclick="eliminarRazonamiento(' + i + ', ' + j + ')"><i class="fas fa-trash-alt mr-2"></i>Eliminar Razonamiento</button>' +
        '</div>';
    return divEliminarRazonamiento;
}

function nuevaSolucion() {
    var containerSolucion;
    var titulo;
    var claseTitulo;
    var claseContainerSolucion;
    var divEliminarSolucion;

    var idSolucion = 1;
    while (document.querySelector("#solucion" + idSolucion) !== null) {
        idSolucion++;
    }

    numRazonamientos[idSolucion - 1] = 0;
    numSoluciones++;

    containerSolucion = crearContainerSolucion(false, "", idSolucion);
    containerSolucion.setAttribute("data-id", "0");

    divEliminarSolucion = crearDivEliminarSolucion(idSolucion);
    containerSolucion.appendChild(document.createElement("hr"));
    containerSolucion.appendChild(divEliminarSolucion);

    var sigContainerSolucion = document.querySelector("#solucion" + (idSolucion + 1));
    if (sigContainerSolucion !== null) {
        sectionCuestion.insertBefore(containerSolucion, sigContainerSolucion);
    } else {
        sectionCuestion.appendChild(containerSolucion);
    }

    containerSolucion.scrollIntoView();
}

function eliminarSolucion(i) {
    document.querySelector(".modal-title").textContent = "Eliminar Solución " + i;
    document.querySelector(".modal-body").textContent = "¿Estás seguro de que deseas eliminar la solución?: " + document.querySelector("#textSolucion" + i).value;
    document.querySelector(".modal-footer").innerHTML = '<button class="btn btn-danger" data-dismiss="modal" type="button"><i class="fas fa-times mr-2"></i>No</button><button id="btnEliminarModal" class="btn btn-success" type="button"><i class="fas fa-check mr-2"></i>Si</button>';
    document.querySelector("#btnEliminarModal").addEventListener("click", function () {
        eliminarSolucion2(i);
    });
    $('#modalAdvertencia').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function eliminarSolucion2(i) {
    var idSolucion = document.querySelector("#solucion" + i).getAttribute("data-id");
    console.log("Eliminar solucion: " + idSolucion);
    if (idSolucion != 0) {
        httpRequest = new XMLHttpRequest();
        httpRequest.open("DELETE", "/api/t/soluciones/" + idSolucion, true);
        httpRequest.responseType = "json";
        httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
        httpRequest.onload = function () {
            comprobarCodEliminarSolucion(i);
        };
        httpRequest.send();
    } else {
        eliminarContainerSolucion(i);
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Solución eliminada correctamente';
        document.querySelector(".modal-footer").innerHTML = '<button class="btn btn-secondary" data-dismiss="modal" type="button"><i class="fas fa-times mr-2"></i>Cerrar</button>';
    }
}

function comprobarCodEliminarSolucion(i) {
    if (httpRequest.status === 204) {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Solución eliminada correctamente';
        eliminarContainerSolucion(i);
    } else {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-exclamation-triangle text-danger h2 mr-2"></i> Error en el servidor. Inténtelo de nuevo más tarde.';
    }
    document.querySelector(".modal-footer").innerHTML = '<button class="btn btn-secondary" data-dismiss="modal" type="button"><i class="fas fa-times mr-2"></i>Cerrar</button>';
}

function eliminarContainerSolucion(i) {
    var containerSolucion = document.querySelector("#solucion" + i);
    eliminar(containerSolucion);
    numSoluciones--;
    if (numSoluciones === 0) {
        document.querySelector("#disponible").checked = false;
    }
}

function eliminar(elem) {
    elem.parentNode.removeChild(elem);
}

function corregirSolucion(i) {
    var solucion = soluciones[i - 1];
    var anteriorContainerSolucion = document.querySelector("#solucion" + i);

    var nuevoContainerSolucion = crearContainerSolucion(false, solucion.respuesta, i);
    nuevoContainerSolucion.setAttribute("data-id", anteriorContainerSolucion.getAttribute("data-id"));
    var divOpcionesSolucion;

    if (document.querySelector("#correcta" + i).checked == true) {
        solucion.correcta = true;
        divOpcionesSolucion = crearDivEliminarSolucion(i);
    } else {
        solucion.correcta = false;
        divOpcionesSolucion = crearDivOpcionesSolucion(i);
    }

    nuevoContainerSolucion.appendChild(document.createElement("hr"));
    nuevoContainerSolucion.appendChild(divOpcionesSolucion);
    sectionCuestion.insertBefore(nuevoContainerSolucion, anteriorContainerSolucion);
    eliminar(anteriorContainerSolucion);

    if (solucion.correcta) {
        document.querySelector("#correcta" + i).checked = true;
    } else {
        document.querySelector("#incorrecta" + i).checked = true;
        añadirRazonamiento(i);
    }

    eliminar(document.querySelector("#dropItem" + i));
}

function añadirRazonamiento(i) {
    var divRazonamiento;
    var divEliminarRazonamiento;
    var sigdivRazonamiento;
    var containerSolucion;
    var divOpcionesSolucion;

    var idRazonamiento = 1;
    while (document.querySelector("#razonamiento" + idRazonamiento + "Solucion" + i) !== null) {
        idRazonamiento++;
    }
    numRazonamientos[i - 1]++;

    divRazonamiento = crearDivRazonamiento(false, "", i, idRazonamiento);
    divRazonamiento.setAttribute("data-id", "0");

    divEliminarRazonamiento = crearDivEliminarRazonamiento(i, numRazonamientos[i - 1]);
    divRazonamiento.appendChild(divEliminarRazonamiento);

    sigdivRazonamiento = document.querySelector("#razonamiento" + (idRazonamiento + 1) + "Solucion" + i);
    containerSolucion = document.querySelector("#solucion" + i);
    divOpcionesSolucion = document.querySelector("#opciones" + i);
    if (sigdivRazonamiento !== null) {
        containerSolucion.insertBefore(divRazonamiento, sigdivRazonamiento);
        containerSolucion.insertBefore(document.createElement("hr"), sigdivRazonamiento);
    } else {
        containerSolucion.insertBefore(divRazonamiento, divOpcionesSolucion);
        containerSolucion.insertBefore(document.createElement("hr"), divOpcionesSolucion);
    }
    divRazonamiento.scrollIntoView();
}

function corregirRazonamiento(i, j) {
    var razonamiento = razonamientos[i - 1][j - 1];
    var containerSolucion = document.querySelector("#solucion" + i);
    var anteriorDivRazonamiento = document.querySelector("#razonamiento" + j + "Solucion" + i);

    var nuevoDivRazonamiento = crearDivRazonamiento(false, razonamiento.texto, i, j);
    nuevoDivRazonamiento.setAttribute("data-id", anteriorDivRazonamiento.getAttribute("data-id"));

    if (document.querySelector("#justificado" + j + "Solucion" + i).checked == true) {
        razonamiento.justificado = true;
    } else {
        razonamiento.justificado = false;
        var divError = crearDivError(i, j, "");
        nuevoDivRazonamiento.appendChild(divError);
    }

    var divEliminarRazonamiento = crearDivEliminarRazonamiento(i, j);
    nuevoDivRazonamiento.appendChild(divEliminarRazonamiento);

    containerSolucion.insertBefore(nuevoDivRazonamiento, anteriorDivRazonamiento);
    eliminar(anteriorDivRazonamiento);

    if (razonamiento.justificado) {
        document.querySelector("#justificado" + j + "Solucion" + i).checked = true;
    } else {
        document.querySelector("#injustificado" + j + "Solucion" + i).checked = true;
    }
    eliminar(document.querySelector("#dropItem" + j + "Solucion" + i));
}

function eliminarRazonamiento(i, j) {
    document.querySelector(".modal-title").textContent = "Eliminar Razonamiento " + j + " de la Solución " + i;
    document.querySelector(".modal-body").textContent = "¿Estás seguro de que deseas eliminar el razonamiento?: " + document.querySelector("#textoRazonamiento" + j + "Solucion" + i).value;
    document.querySelector(".modal-footer").innerHTML = '<button class="btn btn-danger" data-dismiss="modal" type="button"><i class="fas fa-times mr-2"></i>No</button><button id="btnEliminarModal" class="btn btn-success" type="button"><i class="fas fa-check mr-2"></i>Si</button>';
    document.querySelector("#btnEliminarModal").addEventListener("click", function () {
        eliminarRazonamiento2(i, j);
    });
    $('#modalAdvertencia').modal({
        backdrop: 'static',
        keyboard: false
    });
}

function eliminarRazonamiento2(i, j) {
    var idRazonamiento = document.querySelector("#razonamiento" + j + "Solucion" + i).getAttribute("data-id");
    console.log("Eliminar Razonamiento: " + idRazonamiento);
    if (idRazonamiento != 0) {
        httpRequest = new XMLHttpRequest();
        httpRequest.open("DELETE", "/api/t/razonamientos/" + idRazonamiento, true);
        httpRequest.responseType = "json";
        httpRequest.setRequestHeader("X-Token", usuarioIdentificado.jwt);
        httpRequest.onload = function () {
            comprobarCodEliminarRazonamiento(i, j);
        };
        httpRequest.send();
    } else {
        eliminarDivRazonamiento(i, j);
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Razonamiento eliminado correctamente';
        document.querySelector(".modal-footer").innerHTML = '<button class="btn btn-secondary" data-dismiss="modal" type="button"><i class="fas fa-times mr-2"></i>Cerrar</button>';
    }
}

function comprobarCodEliminarRazonamiento(i, j) {
    if (httpRequest.status === 204) {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Razonamiento eliminado correctamente';
        eliminarDivRazonamiento(i, j);
    } else {
        document.querySelector(".modal-body").innerHTML = '<i class="fas fa-exclamation-triangle text-danger h2 mr-2"></i> Error en el servidor. Inténtelo de nuevo más tarde.';
    }
    document.querySelector(".modal-footer").innerHTML = '<button class="btn btn-secondary" data-dismiss="modal" type="button"><i class="fas fa-times mr-2"></i>Cerrar</button>';
}

function eliminarDivRazonamiento(i, j) {
    var divRazonamiento = document.querySelector("#razonamiento" + j + "Solucion" + i);
    if (divRazonamiento !== null) {
        eliminar(divRazonamiento.nextSibling);
        eliminar(divRazonamiento);
        numRazonamientos[i - 1]--;
    }
    if (numRazonamientos[i - 1] == 0) {
        marcarSolucionCorrecta(i);
        document.querySelector("#correcta" + i).checked = true;
    }
}

function marcarDisponibleParaAprendices() {
    if (numSoluciones === 0) {
        nuevaSolucion();
    }
}

function marcarSolucionIncorrecta(i) {
    var containerSolucion = document.querySelector("#solucion" + i);
    if (!containerSolucion.classList.contains("propuestaPorAlumno") && numRazonamientos[i - 1] == 0) {
        añadirRazonamiento(i);
        var anterioresOpciones = document.querySelector("#opciones" + i);
        var nuevasOpciones = crearDivOpcionesSolucion(i);
        containerSolucion.insertBefore(nuevasOpciones, anterioresOpciones);
        eliminar(anterioresOpciones);
    }
}

function marcarSolucionCorrecta(i) {
    var containerSolucion = document.querySelector("#solucion" + i);
    var j = 1;
    if (!containerSolucion.classList.contains("propuestaPorAlumno")) {
        while (numRazonamientos[i - 1] != 0) {
            eliminarRazonamiento(i, j);
            j++;
        }
        var anterioresOpciones = document.querySelector("#opciones" + i);
        var nuevasOpciones = crearDivEliminarSolucion(i);
        containerSolucion.appendChild(nuevasOpciones);
        eliminar(anterioresOpciones);
    }
}

function marcarRazonamientoInjustificado(i, j) {
    var divRazonamiento = document.querySelector("#razonamiento" + j + "Solucion" + i);
    var divEliminar = document.querySelector("#eliminar" + j + "Solucion" + i);
    var divError = document.querySelector("#error" + j + "Solucion" + i);

    if (!divRazonamiento.classList.contains("propuestoPorAlumno") && divError === null) {
        divError = crearDivError(i, j, "");
        divRazonamiento.insertBefore(divError, divEliminar);
    }
}

function marcarRazonamientoJustificado(i, j) {
    var divError = document.querySelector("#error" + j + "Solucion" + i);
    if (divError !== null) {
        eliminar(divError);
    }
}

function guardarCuestion() {
    var cuestion = {};

    cuestion.id = Number(idCuestion);
    cuestion.enunciado = document.querySelector("#enunciado").value;
    cuestion.disponible = document.querySelector("#disponible").checked;
    cuestion.soluciones = [];

    var i = 1;
    var numSolucionesGuardadas = 0;

    while (numSolucionesGuardadas < numSoluciones) {
        var containerSolucion = document.querySelector("#solucion" + i);
        if (containerSolucion !== null) {
            var solucion = {};
            solucion.id = i;
            solucion.respuesta = document.querySelector("#textSolucion" + i).value;
            if (containerSolucion.classList.contains("propuestaPorAlumno")) {
                solucion.propuestaPorAlumno = true;
            } else {
                solucion.propuestaPorAlumno = false;
                if (document.querySelector("#correcta" + i).checked) {
                    solucion.correcta = true;
                } else {
                    solucion.correcta = false;
                    solucion.razonamientos = [];

                    var j = 1;
                    var numRazonamientosGuardados = 0;

                    while (numRazonamientosGuardados < numRazonamientos[i - 1]) {
                        var divRazonamiento = document.querySelector("#razonamiento" + j + "Solucion" + i);
                        if (divRazonamiento !== null) {

                            var razonamiento = {};
                            razonamiento.id = j;
                            razonamiento.texto = document.querySelector("#textoRazonamiento" + j + "Solucion" + i).value;

                            if (divRazonamiento.classList.contains("propuestoPorAlumno")) {
                                razonamiento.propuestoPorAlumno = true;
                            } else {
                                razonamiento.propuestoPorAlumno = false;
                                if (document.querySelector("#justificado" + j + "Solucion" + i).checked) {
                                    razonamiento.justificado = true;
                                } else {
                                    razonamiento.justificado = false;
                                    razonamiento.error = document.querySelector("#textoError" + j + "Solucion" + i).value;
                                }
                            }
                            solucion.razonamientos.push(razonamiento);
                            numRazonamientosGuardados++;
                        }
                        j++;
                    }
                }
            }
            cuestion.soluciones.push(solucion);
            numSolucionesGuardadas++;
        }
        i++;
    }

    datos.cuestiones[idCuestion - 1] = cuestion;
    localStorage.setItem("datos", JSON.stringify(datos));

    return true;
}

function crearDropItemNuevaPropuesta(esSolucion, i, j) {
    var dropdown = document.querySelector("#dropdownNuevasPropuestas");
    var dropItem = document.createElement("a");
    var href;
    var texto;
    var id;

    document.querySelector(".dropdown").classList.remove("oculto");

    if (esSolucion) {
        id = "dropItem" + i;
        href = "#solucion" + i;
        texto = "Solución " + i;
    } else {
        id = "dropItem" + j + "Solucion" + i;
        href = "#razonamiento" + j + "Solucion" + i;
        texto = "Razonamiento " + j + " de la Solución " + i;
    }
    dropItem.id = id;
    dropItem.className = "dropdown-item bg-info text-light";
    dropItem.href = href;
    dropItem.textContent = texto;

    dropdown.appendChild(dropItem);
}
