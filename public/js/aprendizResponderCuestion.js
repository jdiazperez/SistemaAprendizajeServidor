var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));
var idCuestion;
var cuestion;
var sectionCuestion;
var soluciones;
var razonamientos = [];

function mostrarCuestion() {
    cuestion = JSON.parse(localStorage.getItem("responderCuestion"));
    idCuestion = cuestion.id;

    sectionCuestion = document.querySelector("#sectionCuestion");

    añadirEnunciado();
    añadirPropuestaSolucion();
    getSoluciones();
}

function añadirEnunciado() {
    var containerEnunciado = document.createElement("div");
    containerEnunciado.className = "container bg-light border p-4 mt-2";
    containerEnunciado.innerHTML =
        '<div class="container bg-light border p-4 mt-2">' +
        '<div class="form-group">' +
        '<label for="enunciado"><h4 class="text-primary">Enunciado</h4></label>' +
        '<input type="text" id="enunciado" class="form-control" readonly value="' + cuestion.enunciado + '">' +
        '</div>' +
        '</div>';
    sectionCuestion.appendChild(containerEnunciado);
}

function añadirPropuestaSolucion() {
    var containerPropuestaSolucion = document.createElement("div");
    containerPropuestaSolucion.id = "propuestaSolucion";
    containerPropuestaSolucion.className = "container bg-light border p-4 mt-4";
    containerPropuestaSolucion.innerHTML =
        '<form id="formPropuestaSolucion" onsubmit="return enviarPropuestaSolucion()">' +
        '<div class="form-group">' +
        '<label for="textoPropuestaSolucion"><h4 class="text-primary">Propuesta de Solución</h4></label>' +
        '<textarea id="textoPropuestaSolucion" rows="3" class="form-control" placeholder="Introduce la respuesta a la pregunta" required></textarea>' +
        '</div>' +
        '<div class="row mt-3 ml-1">' +
        '<button id="btnPropuestaSolucion" type="submit" form="formPropuestaSolucion" class="btn btn-success"><i class="fas fa-share-square mr-2"></i>Enviar Solución</button>' +
        '</div>' +
        '<div id="alertPropuestaSolucion" class="alert alert-info mt-3 ml-1 oculto">' +
        'Se ha enviado para su corrección manual' +
        '</div>' +
        '<div id="alertErrorPropuestaSolucion" class="alert alert-danger mt-3 ml-1 oculto">' +
        'No se ha podido guardar en el servidor' +
        '</div>' +
        '</div>' +
        '</form>';
    sectionCuestion.appendChild(containerPropuestaSolucion);
}

function enviarPropuestaSolucion() {
    var alert = document.querySelector("#alertPropuestaSolucion");
    var btn = document.querySelector("#btnPropuestaSolucion");
    var texto = document.querySelector("#textoPropuestaSolucion");
    var solucion = {};

    alert.classList.remove("oculto");
    btn.disabled = true;
    texto.disabled = true;

    solucion.respuesta = texto.value;
    solucion.correcta = false;
    solucion.propuestaPorAlumno = true;
    solucion.idCuestion = idCuestion;

    guardarSolucion(solucion);

    mostrarSolucion(1);

    return false;
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
        añadirSoluciones();
        var requestRazonamientos = [];
        for (var i = 0; i < soluciones.length; i++) {
            if (!soluciones[i].correcta && !soluciones[i].propuestaPorAlumno) {
                requestRazonamientos[i] = new XMLHttpRequest();
                getRazonamientos(requestRazonamientos[i], i);
            } else {
                razonamientos[i] = [];
            }
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
        añadirRazonamientos(i + 1);

    } else {
        document.querySelector("#alertError").classList.remove("oculto");
    }
}

function guardarSolucion(solucion) {
    $.ajax({
        type: 'POST',
        url: "/api/t/soluciones",
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        data: JSON.stringify(solucion),
        beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Token", usuarioIdentificado.jwt);
        },
        complete: function (xhr) {
            if (xhr.status != 201) {
                document.querySelector("#alertPropuestaSolucion").classList.add("oculto");
                document.querySelector("#alertErrorPropuestaSolucion").classList.remove("oculto");
            }
        }
    });
}

function añadirSoluciones() {
    var containerSolucion;
    var solucion;
    for (var i = 1; i <= soluciones.length; i++) {
        solucion = soluciones[i - 1];
        if (!solucion.propuestaPorAlumno) {
            containerSolucion = crearContainerSolucion(i, solucion.respuesta);
            sectionCuestion.appendChild(containerSolucion);
        }
    }
}

function crearContainerSolucion(i, respuesta) {
    var containerSolucion = document.createElement("div");
    containerSolucion.id = "solucion" + i;
    containerSolucion.className = "container bg-light border p-4 mt-4 oculto";
    containerSolucion.innerHTML =
        '<div class="form-group">' +
        '<label for="textoSolucion' + i + '"><h4 class="text-primary">Solución ' + i + '</h4></label>' +
        '<textarea id="textoSolucion' + i + '" rows="3" class="form-control" readonly>' + respuesta + '</textarea>' +
        '</div>' +
        '<div class="row">' +
        '<div class="custom-control custom-radio ml-3">' +
        '<input type="radio" id="correcta' + i + '" name="tipoSolucion' + i + '" class="custom-control-input" required>' +
        '<label for="correcta' + i + '" class="custom-control-label">Solución Correcta</label>' +
        '</div>' +
        '<div class="custom-control custom-radio ml-3">' +
        '<input type="radio" id="incorrecta' + i + '" name="tipoSolucion' + i + '" class="custom-control-input">' +
        '<label for="incorrecta' + i + '" class="custom-control-label">Solución Incorrecta</label>' +
        '</div>' +
        '</div>' +
        '<div class="row mt-3 ml-1">' +
        '<button id="btnCorregir' + i + '" class="btn btn-success" onclick="corregirSolucion(' + i + ')"><i class="fas fa-check-circle mr-2"></i>Corregir</button>' +
        '</div>' +
        '<div id="alertAcierto' + i + '" class="alert alert-success mt-3 ml-1 oculto">' +
        '<strong>Acierto</strong>' +
        '</div>' +
        '<div id="alertFallo' + i + '" class="alert alert-danger mt-3 ml-1 oculto">' +
        '<strong>Fallo</strong>' +
        '</div>';
    return containerSolucion;
}

function corregirSolucion(i) {
    var radioCorrecta = document.querySelector("#correcta" + i);
    var radioIncorrecta = document.querySelector("#incorrecta" + i);
    var btnCorregir = document.querySelector("#btnCorregir" + i);
    var solucion = soluciones[i - 1];

    if (radioCorrecta.validity.valid) {
        if ((radioCorrecta.checked && solucion.correcta) || (radioIncorrecta.checked && !solucion.correcta)) {
            var alertAcierto = document.querySelector("#alertAcierto" + i);
            alertAcierto.classList.remove("oculto");
        } else {
            var alertFallo = document.querySelector("#alertFallo" + i);
            alertFallo.classList.remove("oculto");
        }
        btnCorregir.disabled = true;
        radioCorrecta.disabled = true;
        radioIncorrecta.disabled = true;

        if (!solucion.correcta) {
            mostrarPropuestaRazonamiento(i);
        } else {
            mostrarSolucion(i + 1);
        }
    }
}

function añadirRazonamientos(i) {
    var containerSolucion = document.querySelector("#solucion" + i);
    var divPropuestaRazonamiento = crearDivPropuestaRazonamiento(i);
    containerSolucion.appendChild(divPropuestaRazonamiento);

    for (var j = 1; j <= razonamientos[i - 1].length; j++) {
        var razonamiento = razonamientos[i - 1][j - 1];

        if (!razonamiento.propuestoPorAlumno) {
            var divRazonamiento = crearDivRazonamiento(i, j, razonamiento.texto);
            containerSolucion.appendChild(divRazonamiento);

            if (!razonamiento.justificado) {
                var divError = crearDivError(i, j, razonamiento.error);
                containerSolucion.appendChild(divError);
            }
        }
    }
}

function crearDivPropuestaRazonamiento(i) {
    var divPropuestaRazonamiento = document.createElement("div");
    divPropuestaRazonamiento.id = "propuestaRazonamiento" + i;
    divPropuestaRazonamiento.className = "oculto";

    divPropuestaRazonamiento.innerHTML =
        '<div class="form-group mt-5 ml-2">' +
        '<label for="textoPropuestaRazonamiento' + i + '"><h5 class="text-info">Propuesta de Razonamiento</h5></label>' +
        '<textarea id="textoPropuestaRazonamiento' + i + '" rows="3" class="form-control" placeholder="¿Por qué la solución es incorrecta?. Escribe un razonamiento" required></textarea>' +
        '</div>' +
        '<div class="row mt-3 ml-2">' +
        '<button id="btnPropuestaRazonamiento' + i + '" type="button" class="btn btn-success" onclick="enviarPropuestaRazonamiento(' + i + ')"><i class="fas fa-share-square mr-2"></i>Enviar Razonamiento</button>' +
        '</div>' +
        '<div id="alertPropuestaRazonamiento' + i + '" class="alert alert-info mt-3 ml-1 oculto">Se ha enviado para su corrección manual</div>' +
        '<div id="alertErrorPropuestaRazonamiento' + i + '" class="alert alert-danger mt-3 ml-1 oculto">No se ha podido guardar en el servidor</div>';

    return divPropuestaRazonamiento;
}

function enviarPropuestaRazonamiento(i) {
    var alert = document.querySelector("#alertPropuestaRazonamiento" + i);
    var btn = document.querySelector("#btnPropuestaRazonamiento" + i);
    var texto = document.querySelector("#textoPropuestaRazonamiento" + i);
    var solucion = soluciones[i - 1];
    var razonamiento = {};

    alert.classList.remove("oculto");
    btn.disabled = true;
    texto.disabled = true;

    razonamiento.texto = texto.value;
    razonamiento.justificado = false;
    razonamiento.error = "";
    razonamiento.propuestoPorAlumno = true;
    razonamiento.idSolucion = soluciones[i - 1].id;

    guardarRazonamiento(razonamiento, i);

    var razonamientoMostrado = mostrarRazonamiento(i, 1);
    if (razonamientoMostrado === false) {
        mostrarSolucion(i + 1);
    }
}

function guardarRazonamiento(razonamiento, i) {
    $.ajax({
        type: 'POST',
        url: "/api/t/razonamientos",
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        data: JSON.stringify(razonamiento),
        beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Token", usuarioIdentificado.jwt);
        },
        complete: function (xhr) {
            if (xhr.status != 201) {
                document.querySelector("#alertPropuestaRazonamiento" + i).classList.add("oculto");
                document.querySelector("#alertErrorPropuestaRazonamiento" + i).classList.remove("oculto");
            }
        }
    });
}

function crearDivRazonamiento(i, j, texto) {
    var divRazonamiento = document.createElement("div");
    divRazonamiento.id = "razonamiento" + j + "Solucion" + i;
    divRazonamiento.className = "oculto";
    divRazonamiento.innerHTML =
        '<div class="form-group mt-5 ml-2">' +
        '<label for="textoRazonamiento' + j + 'Solucion' + i + '"><h5 class="text-info">Razonamiento ' + j + '</h5></label>' +
        '<textarea id="textoRazonamiento' + j + 'Solucion' + i + '" rows="3" class="form-control" readonly>' + texto + '</textarea>' +
        '</div>' +
        '<div class="row">' +
        '<div class="custom-control custom-radio ml-4">' +
        '<input type="radio" id="justificado' + j + 'Solucion' + i + '" name="tipoRazonamiento' + j + 'Solucion' + i + '" class="custom-control-input" required>' +
        '<label for="justificado' + j + 'Solucion' + i + '" class="custom-control-label">Razonamiento Justificado</label>' +
        '</div>' +
        '<div class="custom-control custom-radio ml-4">' +
        '<input type="radio" id="injustificado' + j + 'Solucion' + i + '" name="tipoRazonamiento' + j + 'Solucion' + i + '" class="custom-control-input">' +
        '<label for="injustificado' + j + 'Solucion' + i + '" class="custom-control-label">Razonamiento Injustificado</label>' +
        '</div>' +
        '</div>' +
        '<div class="row mt-3 ml-2">' +
        '<button id="btnCorregir' + j + 'Solucion' + i + '" class="btn btn-success" onclick="corregirRazonamiento(' + i + ', ' + j + ')"><i class="fas fa-check-circle mr-2"></i>Corregir</button>' +
        '</div>' +
        '<div id="alertAcierto' + j + 'Solucion' + i + '" class="alert alert-success mt-3 ml-1 oculto">' +
        '<strong>Acierto</strong>' +
        '</div>' +
        '<div id="alertFallo' + j + 'Solucion' + i + '" class="alert alert-danger mt-3 ml-1 oculto">' +
        '<strong>Fallo</strong>' +
        '</div>';
    return divRazonamiento;
}

function corregirRazonamiento(i, j) {
    var radioJustificado = document.querySelector("#justificado" + j + "Solucion" + i);
    var radioInjustificado = document.querySelector("#injustificado" + j + "Solucion" + i);
    var btnCorregir = document.querySelector("#btnCorregir" + j + "Solucion" + i);
    var razonamiento = razonamientos[i - 1][j - 1];

    if (radioJustificado.validity.valid) {
        if ((radioJustificado.checked && razonamiento.justificado) || (radioInjustificado.checked && !razonamiento.justificado)) {
            var alertAcierto = document.querySelector("#alertAcierto" + j + "Solucion" + i);
            alertAcierto.classList.remove("oculto");
        } else {
            var alertFallo = document.querySelector("#alertFallo" + j + "Solucion" + i);
            alertFallo.classList.remove("oculto");
        }
        btnCorregir.disabled = true;
        radioJustificado.disabled = true;
        radioInjustificado.disabled = true;

        if (!razonamiento.justificado) {
            var divError = document.querySelector("#error" + j + "Solucion" + i);
            divError.classList.remove("oculto");
        }

        var razonamientoMostrado = mostrarRazonamiento(i, j + 1);
        if (razonamientoMostrado === false) {
            mostrarSolucion(i + 1);
        }
    }
}

function crearDivError(i, j, textoError) {
    var divError = document.createElement("div");
    divError.classList = "ml-2 oculto";
    divError.id = "error" + j + "Solucion" + i;
    divError.innerHTML =
        '<div class="form-group mt-4 ml-2">' +
        '<label for="textoError' + j + 'Solucion' + i + '"><h6 class="text-danger">Por qué el razonamiento no está bien justificado:</h6></label>' +
        '<textarea id="textoError' + j + 'Solucion' + i + '" rows="3" class="form-control" readonly>' + textoError + '</textarea>' +
        '</div>';
    return divError;
}

function mostrarSolucion(i) {
    var containerSolucion = document.querySelector("#solucion" + i);
    if (containerSolucion !== null) {
        containerSolucion.classList.remove("oculto");
    }
}

function mostrarPropuestaRazonamiento(i) {
    var containerSolucion = document.querySelector("#solucion" + i);
    var divPropuestaRazonamiento = document.querySelector("#propuestaRazonamiento" + i);

    containerSolucion.insertBefore(document.createElement("hr"), divPropuestaRazonamiento);
    divPropuestaRazonamiento.classList.remove("oculto");
}

function mostrarRazonamiento(i, j) {
    var mostradoCorrectamente = false;
    var divRazonamiento = document.querySelector("#razonamiento" + j + "Solucion" + i);
    if (divRazonamiento !== null) {
        var containerSolucion = document.querySelector("#solucion" + i);
        containerSolucion.insertBefore(document.createElement("hr"), divRazonamiento);
        divRazonamiento.classList.remove("oculto");
        mostradoCorrectamente = true;
    }
    return mostradoCorrectamente;
}
