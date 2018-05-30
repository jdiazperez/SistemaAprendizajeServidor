function cargar() {
    var datos = {
        usuarios: [
            {
                clave: 1,
                nombre: "m",
                contraseña: "m",
                tipo: "maestro"
            },
            {
                clave: 2,
                nombre: "a",
                contraseña: "a",
                tipo: "aprendiz"
            },
            {
                clave: 3,
                nombre: "b",
                contraseña: "b",
                tipo: "aprendiz"
            },
            {
                clave: 4,
                nombre: "c",
                contraseña: "c",
                tipo: "aprendiz"
            },
            ],
        cuestiones: [
            { // cuestion
                id: 1,
                enunciado: "¿Qué es el software?",
                disponible: true,
                soluciones: [
                    { //solucion
                        id: 1,
                        respuesta: "El software es la parte lógica de un sistema informático, o sea, sin contemplar el hardware",
                        correcta: false,
                        propuestaPorAlumno: false,
                        razonamientos: [
                            { //razonamiento
                                id: 1,
                                texto: "La definición es demasiado permisiva porque incluye firmware, que no es software",
                                justificado: false,
                                error: "El firmware sí es software pero con la característica de ser muy acoplado a algún dispositivo hardware",
                                propuestoPorAlumno: false
                                },
                            { //razonamiento
                                id: 2,
                                texto: "La definición es demasiado permisiva porque los datos de usuario no son hardware ni software",
                                justificado: true,
                                propuestoPorAlumno: false
                                }
                            ]
                        },
                    { //solucion
                        id: 2,
                        respuesta: "El software es la información que el desarrollador suministra al hardware para posteriormente manipular la información del usuario",
                        correcta: true,
                        propuestaPorAlumno: false
                        },
                    { //solucion
                        id: 3,
                        respuesta: "El software es el conjunto de los programas",
                        correcta: false,
                        propuestaPorAlumno: false,
                        razonamientos: [
                            { //razonamiento
                                id: 1,
                                texto: "Porque no contempla los scripts de bases de datos, ficheros de configuración, ficheros de datos ni otros artefactos",
                                justificado: true,
                                propuestoPorAlumno: false
                                }
                            ]
                        }
                    ]
                },
            { // cuestion
                id: 2,
                enunciado: "¿Qué es la recursividad?",
                disponible: true,
                soluciones: [
                    { //solucion
                        id: 1,
                        respuesta: "La característica de una función que se llama a sí misma",
                        correcta: false,
                        propuestaPorAlumno: false,
                        razonamientos: [
                            { //razonamiento
                                id: 1,
                                texto: "La definición es demasiado restrictiva porque no contempla la recursividad mutua",
                                justificado: true,
                                propuestoPorAlumno: false
                                }
                            ]
                        },
                    { //solucion
                        id: 2,
                        respuesta: "La característica de una función que se llama a sí misma, directa o indirectamente a través de otras",
                        correcta: false,
                        propuestaPorAlumno: false,
                        razonamientos: [
                            { //razonamiento
                                id: 1,
                                texto: "La definición es demasiado restrictiva porque no contempla recursividad de datos o de imágenes",
                                justificado: true,
                                propuestoPorAlumno: false
                                }
                            ]
                        },
                    { //solucion
                        id: 3,
                        respuesta: "La característica de algo que se define sobre sí mismo, directa o indirectamente",
                        correcta: true,
                        propuestaPorAlumno: false,

                        }
                    ]
                },
            { // cuestion
                id: 3,
                enunciado: "¿Qué es la Programación Orientada a Objetos?",
                disponible: true,
                soluciones: [
                    { //solucion
                        id: 1,
                        respuesta: "Es un paradigma de programación donde los objetos son entidades que tienen un determinado estado, comportamiento e identidad",
                        correcta: false,
                        propuestaPorAlumno: false,
                        razonamientos: [
                            { //razonamiento
                                id: 1,
                                texto: "Demasiado ambigüa",                               
                                propuestoPorAlumno: true
                                }
                            ]
                        },
                    { //solucion
                        id: 2,
                        respuesta: "Es un paradigma de programación donde los objetos son abstracciones de entidades del mundo real",
                        propuestaPorAlumno: true
                        }
                    ]
                }
             ]
    }

    localStorage.setItem("datos", JSON.stringify(datos));
    alert("Cargados usuarios y cuestiones");
    console.log(JSON.parse(localStorage.getItem("datos")));
}
