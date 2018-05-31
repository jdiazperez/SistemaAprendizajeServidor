function actualizarUsuarioIdentificado() {
    var navLinkUsuario = document.querySelector("#usuarioIdentificado");
    var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));
    var rolUsuario;
    if(usuarioIdentificado.usuario.maestro){
        rolUsuario = "Maestro";
    }
    else{
        rolUsuario = "Aprendiz";
    }
    navLinkUsuario.textContent = rolUsuario + ": " + usuarioIdentificado.usuario.nombreUsuario;
}