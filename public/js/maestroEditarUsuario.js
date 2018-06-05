var httpRequest;
var usuarioIdentificado = JSON.parse(localStorage.getItem("usuarioIdentificado"));
var usuario = JSON.parse(localStorage.getItem("editarUsuario"));

var textNombreUsuario;
var radioMaestro;
var radioAprendiz;
var checkActivo;
var textCorreo;
var textNombre;
var textApellidos;
var textTelefono;

function mostrarUsuario() {
    textNombreUsuario = document.querySelector("#nombreUsuario");
    textNombreUsuario.value = usuario.nombreUsuario;
    radioMaestro = document.querySelector("#maestro");
    radioAprendiz = document.querySelector("#aprendiz");
    if (usuario.maestro) {
        radioMaestro.checked = true;
    } else {
        radioAprendiz.checked = true;
    }
    checkActivo = document.querySelector("#activo");
    if (usuario.activo) {
        checkActivo.checked = true;
    }
    textCorreo = document.querySelector("#correo");
    textCorreo.value = usuario.correo;
    textNombre = document.querySelector("#nombre");
    textNombre.value = usuario.nombre;
    textApellidos = document.querySelector("#apellidos");
    textApellidos.value = usuario.apellidos;
    textTelefono = document.querySelector("#telefono");
    textTelefono.value = usuario.telefono;
}

function actualizarUsuario() {
    usuario.nombreUsuario = textNombreUsuario.value;
    usuario.maestro = radioMaestro.checked;
    usuario.activo = checkActivo.checked;
    usuario.nombre = textNombre.value;
    usuario.apellidos = textApellidos.value;
    usuario.correo = textCorreo.value;
    usuario.telefono = telefono.value;
    
    $.ajax({
        type: 'PUT',
        url: "/api/t/maestro/usuarios/" + usuario.id,
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        data: JSON.stringify(usuario),
        beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Token", usuarioIdentificado.jwt);
        },
        complete: function (xhr) {
            if (xhr.status == 201) {
                document.querySelector(".modal-body").innerHTML = '<i class="fas fa-check-circle text-success h2 mr-2"></i> Usuario actualizado correctamente.';
            } else {
                document.querySelector(".modal-body").innerHTML = '<i class="fas fa-exclamation-triangle text-danger h2 mr-2"></i> Error en el servidor. Inténtelo de nuevo más tarde.';
            }
            document.querySelector(".modal-footer").innerHTML = '<a class="btn btn-secondary" href="/maestroGestionUsuarios">Volver</a>';
        }
    });

    $('#modalUsuario').modal({
        backdrop: 'static',
        keyboard: false
    });

    return false;
}

