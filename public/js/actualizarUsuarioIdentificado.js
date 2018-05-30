function actualizarUsuarioIdentificado() {
    var navLinkUsuario = document.querySelector("#usuarioIdentificado");
    var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));
    navLinkUsuario.textContent = usuarioIdentificado.tipo + ": " + usuarioIdentificado.nombre;
}