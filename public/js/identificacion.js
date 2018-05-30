
function validacion(){
    var datos = JSON.parse(window.localStorage.getItem("datos"));
    var nombre = document.querySelector("#usuario").value;
    var contraseña = document.querySelector("#contraseña").value;
    
    var usuario = getUsuario(datos, nombre, contraseña);
    console.log(usuario);

    if (usuario == null){        
        nombre.value = "";
        contraseña.value = "";
        login.action = "index.html";
    } else {        
        localStorage.setItem("usuarioIdentificado", JSON.stringify(usuario));
        var login = document.querySelector("form");
        if (usuario.tipo == "maestro"){
            login.action = "listarCuestionesMaestro.html";
        } else {
            login.action = "listarCuestionesAprendiz.html";
        }
        
    }
    
    return usuario != null;
}

function getUsuario(datos, nombre, contraseña){
    for(usuario of datos.usuarios){
        if (usuario.nombre == nombre &&
           usuario.contraseña == contraseña){
            return usuario;
        }
    }
    return null;
}