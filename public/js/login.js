var httpRequest;

function login() {
    var form = document.querySelector("#formLogin");
    var formData = new FormData(form);
    httpRequest = new XMLHttpRequest();
    httpRequest.open("POST", "/api/t/login", true);
    httpRequest.responseType = "json";
    httpRequest.onload = comprobarCodLogin;
    httpRequest.send(formData);
    return false;
}

function comprobarCodLogin() {
    var alertAccesoDenegado = document.querySelector("#alertAccesoDenegado");
    var alertDatosIncorrectos = document.querySelector("#alertDatosIncorrectos");
    if (httpRequest.status === 200) {        
        var usuarioIdentificado = httpRequest.response;
        localStorage.setItem("usuarioIdentificado", JSON.stringify(usuarioIdentificado));
        if(usuarioIdentificado.usuario.maestro){
            window.location.href = "/maestroGestionCuestiones.html";
        }
        else{
            window.location.href = "/listarCuestionesAprendiz.html";
        }

        
    } else if (httpRequest.status === 403) {
        alertAccesoDenegado.classList.remove("oculto");
        alertDatosIncorrectos.classList.add("oculto");
    } else {
        alertDatosIncorrectos.classList.remove("oculto");
        alertAccesoDenegado.classList.add("oculto");
    }
}
