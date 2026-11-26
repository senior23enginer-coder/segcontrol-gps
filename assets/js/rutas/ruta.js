/****************************************************************************/
/*          Rutas Ajax jquery para opciones paneles                         */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
window.segcontrolVencidosAviso = window.segcontrolVencidosAviso || {
    ultimoTotal: 0,
    ultimoAviso: 0,
    intervaloMinimoMs: 5 * 60 * 1000
};

function reproducirAlertaVencidos() {
    try {
        var AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) {
            return;
        }

        var audioCtx = new AudioContext();
        var masterGain = audioCtx.createGain();
        var notas = [
            { frecuencia: 740, inicio: 0.00, duracion: 0.16 },
            { frecuencia: 980, inicio: 0.20, duracion: 0.18 },
            { frecuencia: 1240, inicio: 0.44, duracion: 0.32 }
        ];

        masterGain.gain.setValueAtTime(0.001, audioCtx.currentTime);
        masterGain.gain.exponentialRampToValueAtTime(0.22, audioCtx.currentTime + 0.03);
        masterGain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.86);
        masterGain.connect(audioCtx.destination);

        notas.forEach(function(nota) {
            var inicio = audioCtx.currentTime + nota.inicio;
            var fin = inicio + nota.duracion;
            var oscillator = audioCtx.createOscillator();
            var harmonic = audioCtx.createOscillator();
            var noteGain = audioCtx.createGain();

            oscillator.type = "triangle";
            oscillator.frequency.setValueAtTime(nota.frecuencia, inicio);
            oscillator.frequency.exponentialRampToValueAtTime(nota.frecuencia * 0.96, fin);

            harmonic.type = "sine";
            harmonic.frequency.setValueAtTime(nota.frecuencia * 1.5, inicio);

            noteGain.gain.setValueAtTime(0.001, inicio);
            noteGain.gain.exponentialRampToValueAtTime(0.55, inicio + 0.025);
            noteGain.gain.exponentialRampToValueAtTime(0.001, fin);

            oscillator.connect(noteGain);
            harmonic.connect(noteGain);
            noteGain.connect(masterGain);

            oscillator.start(inicio);
            harmonic.start(inicio);
            oscillator.stop(fin + 0.02);
            harmonic.stop(fin + 0.02);
        });
    } catch (e) {}
}

function mostrarModalServiciosVencidos(totalVencidos) {
    /*
    var titulo = "Servicios vencidos";
    var etiquetaServicio = "servicio" + (totalVencidos === 1 ? "" : "s");
    var mensaje = "Hay " + totalVencidos + " " + etiquetaServicio + " con minuta vencida. Revisa el panel de vencidos para gestionar el seguimiento.";
    var contenido = [
        "<div class='segcontrol-alert-detail'>",
        "<span class='segcontrol-alert-count'>" + totalVencidos + "</span>",
        "<span class='segcontrol-alert-copy'>",
        "<strong>Atencion requerida</strong>",
        "<span>Hay " + totalVencidos + " " + etiquetaServicio + " con minuta vencida.</span>",
        "<span>Revisa el panel de vencidos para gestionar el seguimiento.</span>",
        "</span>",
        "</div>"
    ].join("");

    if (typeof swal === "function") {
        swal({
            title: titulo,
            text: contenido,
            html: true,
            type: "warning",
            customClass: "segcontrol-alert",
            confirmButtonColor: "#ef3b52",
            confirmButtonText: "Revisar panel"
        });
        return;
    }

    alert(titulo + "\n\n" + mensaje);
    */
    return;
}

function mostrarNotificacionChromeVencidos(totalVencidos) {
    if (!("Notification" in window)) {
        return;
    }

    var titulo = "SEGCONTROL GPS";
    var opciones = {
        body: "Hay " + totalVencidos + " servicio" + (totalVencidos === 1 ? "" : "s") + " vencido" + (totalVencidos === 1 ? "" : "s") + ".",
        tag: "segcontrol-servicios-vencidos",
        requireInteraction: false
    };

    if (Notification.permission === "granted") {
        new Notification(titulo, opciones);
        return;
    }

    if (Notification.permission !== "denied") {
        Notification.requestPermission(function(permission) {
            if (permission === "granted") {
                new Notification(titulo, opciones);
            }
        });
    }
}

window.notificarServiciosVencidos = function(totalVencidos) {
    totalVencidos = parseInt(totalVencidos, 10) || 0;

    if (totalVencidos <= 0) {
        window.segcontrolVencidosAviso.ultimoTotal = 0;
        return;
    }

    var ahora = new Date().getTime();
    var cambioTotal = totalVencidos !== window.segcontrolVencidosAviso.ultimoTotal;
    var pasoIntervalo = ahora - window.segcontrolVencidosAviso.ultimoAviso > window.segcontrolVencidosAviso.intervaloMinimoMs;

    if (!cambioTotal && !pasoIntervalo) {
        return;
    }

    window.segcontrolVencidosAviso.ultimoTotal = totalVencidos;
    window.segcontrolVencidosAviso.ultimoAviso = ahora;

    reproducirAlertaVencidos();
    /*
    mostrarModalServiciosVencidos(totalVencidos);
    */
    mostrarNotificacionChromeVencidos(totalVencidos);
};

function aplicarEstadoSidebar(colapsado) {
    $("body").toggleClass("segcontrol-sidebar-collapsed", colapsado);
    $("#toggleSidebar")
        .attr("aria-expanded", colapsado ? "false" : "true")
        .attr("title", colapsado ? "Mostrar menu" : "Ocultar menu")
        .find(".material-icons")
        .text(colapsado ? "menu" : "menu_open");
}

$(function() {
    var sidebarGuardado = localStorage.getItem("segcontrolSidebarCollapsed") === "1";
    aplicarEstadoSidebar(sidebarGuardado);
});

$(document).on("click", "#toggleSidebar", function(e) {
    e.preventDefault();
    e.stopPropagation();

    var colapsado = !$("body").hasClass("segcontrol-sidebar-collapsed");
    aplicarEstadoSidebar(colapsado);
    localStorage.setItem("segcontrolSidebarCollapsed", colapsado ? "1" : "0");
});

function asegurarModalMinutaRapida() {
    if ($("#modalMinutaRapida").length > 0) {
        return;
    }

    $("body").append(
        "<div class='modal fade segcontrol-quick-minute-modal' id='modalMinutaRapida' tabindex='-1' role='dialog' aria-hidden='true'>" +
            "<div class='modal-dialog modal-xl' role='document'>" +
                "<div class='modal-content'>" +
                    "<div class='modal-header'>" +
                        "<button type='button' class='close' data-dismiss='modal' aria-label='Cerrar'>" +
                            "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                        "<h4 class='modal-title'>Crear minuta rapida</h4>" +
                    "</div>" +
                    "<div class='modal-body' id='contenidoMinutaRapida'>" +
                        "<div class='segcontrol-modal-loading'>Cargando minuta...</div>" +
                    "</div>" +
                "</div>" +
            "</div>" +
        "</div>"
    );
}

function asegurarMenuMinutaRapida() {
    if ($("#menuMinutaRapida").length > 0) {
        return;
    }

    $("body").append(
        "<div id='menuMinutaRapida' class='segcontrol-context-menu'>" +
            "<button type='button' id='abrirMinutaRapida'>" +
                "<i class='material-icons'>note_add</i>" +
                "<span>Crear minuta</span>" +
            "</button>" +
            "<button type='button' id='abrirServicioRapido'>" +
                "<i class='material-icons'>visibility</i>" +
                "<span>Ver servicio</span>" +
            "</button>" +
            "<button type='button' id='cerrarServicioRapido' class='segcontrol-context-danger'>" +
                "<i class='material-icons'>lock</i>" +
                "<span>Cerrar servicio</span>" +
            "</button>" +
        "</div>"
    );
}

function ocultarMenuMinutaRapida() {
    $("#menuMinutaRapida").removeClass("is-visible").removeData("idServicio");
}

function obtenerIdEmpleadoActual() {
    var idEmpleadoSesion = $("#idempleadoHiddenSesion").val();
    if (idEmpleadoSesion) {
        return idEmpleadoSesion;
    }

    return $("#idempleadoHidden").val() || "";
}

function abrirModalMinutaRapida(idServicio) {
    var idEmpleado = obtenerIdEmpleadoActual();

    if (!idServicio || !idEmpleado) {
        swal({
            title: "No se pudo abrir la minuta",
            text: "Falta informacion de sesion o del servicio seleccionado. Recarga la pagina e intenta nuevamente.",
            type: "warning",
            confirmButtonText: "Entendido",
            customClass: "segcontrol-alert"
        });
        return;
    }

    asegurarModalMinutaRapida();
    $("#contenidoMinutaRapida").html("<div class='segcontrol-modal-loading'>Cargando minuta...</div>");
    $("#modalMinutaRapida").modal("show");

    $.ajax({
        data: { ids: idServicio, ide: idEmpleado },
        url: "layout/servicios/nuevaMinuta.php",
        type: "post",
        success: function(response) {
            $("#contenidoMinutaRapida").html(response);
        },
        error: function() {
            $("#contenidoMinutaRapida").html(
                "<div class='alert alert-danger'>No fue posible cargar la minuta. Intenta nuevamente.</div>"
            );
        }
    });
}

$(document).on("contextmenu", ".js-servicio-activo-row", function(e) {
    e.preventDefault();
    e.stopPropagation();

    asegurarMenuMinutaRapida();
    var idServicio = $(this).data("id-servicio");
    $("#menuMinutaRapida")
        .data("idServicio", idServicio)
        .css({
            top: e.pageY + "px",
            left: e.pageX + "px"
        })
        .addClass("is-visible");
});

$(document).on("click", "#abrirMinutaRapida", function(e) {
    e.preventDefault();
    e.stopPropagation();
    var idServicio = $("#menuMinutaRapida").data("idServicio");
    ocultarMenuMinutaRapida();
    abrirModalMinutaRapida(idServicio);
});

$(document).on("click", "#abrirServicioRapido", function(e) {
    e.preventDefault();
    e.stopPropagation();
    var idServicio = $("#menuMinutaRapida").data("idServicio");
    ocultarMenuMinutaRapida();
    abrirBitacoraServicio(idServicio);
});

$(document).on("click", "#cerrarServicioRapido", function(e) {
    e.preventDefault();
    e.stopPropagation();
    var idServicio = $("#menuMinutaRapida").data("idServicio");
    ocultarMenuMinutaRapida();
    confirmarCerrarServicio(idServicio);
});

$(document).on("click scroll", function() {
    ocultarMenuMinutaRapida();
});

$(document).on('click', '#workspaceAdmin', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");

    console.log("Opcion -> administrador");
    $.ajax({
        url: 'administrador.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/****************************************************************************/
$(document).on('click', '#workspaceAsis', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> asistente");
    $.ajax({
        url: 'asistente.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/***************************************************************************/
$(document).on('click', '#workspaceClien', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> cliente");
    $.ajax({
        url: 'cliente.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************************************/
$(document).on('click', '#historialCli', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> cliente");
    $.ajax({
        url: 'layout/servicios/historialCliente.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

$(document).on('click', '#historialCliHis', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> cliente");
    $.ajax({
        url: 'layout/servicios/historiaServicioCliente.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
    //alert(document.getElementById("idnombre").value);
});

/** opcion de navegacion hacia el menu de usuarios***/
$(document).on('click', '#menuUsuarios', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu usuarios");
    $.ajax({
        url: 'layout/panel_usuarios.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
/** opcion de navegacion hacia el menu de empresas***/
$(document).on('click', '#menuEmpresas', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu empresas");
    $.ajax({
        url: 'layout/panel_empresas.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
/** opcion de navegacion hacia el menu de actividades***/
$(document).on('click', '#menuActividades', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu actividades");
    $.ajax({
        url: 'layout/panel_actividades.php?v=' + Date.now(),
        type: 'post',
        cache: false,
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
/** opcion de navegacion hacia el menu de servicios***/
$(document).on('click', '#menuServicios', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu servicios");
    $.ajax({
        url: 'layout/panel_servicios.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

/******************************************************************************/
/*      AJAX modulo de Configuracion  *                                     */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
$(document).on('click', '#menuConfiguracion', function(e) {
    /** opcion de navegacion hacia el menu de configuracion***/
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu configuracion");
    $.ajax({
        url: 'layout/panel_configuracion.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/**********************************************************************/
$(document).on('click', '#correosMain', function(e) {
    console.log("Opcion -> empleado menu principal");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/configuracion/configuracionMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
        }
    });
});
/**********************************************************************/
$(document).on('click', '#nuevoCorreo', function(e) {
    console.log("Opcion -> NuevoCorreo ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/configuracion/nuevoCorreo.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
        }
    });
});
/***********************************************************************/
$(document).on('click', '#crearCorreo', function(e) {
    console.log("Opcion -> crear correo");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");

    var check = 0;
    var correo = document.getElementById("correo").value;
    if (correo.length == 0 || correo == '@') {
        check++;
        document.getElementById("correo").value = '@';
    }

    var clave = document.getElementById("clave").value;
    if (clave.length == 0 || clave == 'REQUERIDO') {
        check++;
        document.getElementById("clave").value = 'REQUERIDO';
    }

    console.log("var " +
        correo + " " +
        clave);
    if (parseInt(check) > 0) {
        alert('No se puede registrar el correo puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "correo": correo,
            "clave": clave
        };
        $.ajax({
            data: params,
            url: '../controller/route/configuracion/crearCorreo.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
            }
        });
        check = 0;
    }
});
/***************************************************************************/
$(document).on('click', '#idCorreo', function(e) {
    console.log("Opcion -> ver Correo");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    var idCorreo = $(this).attr('title');
    console.log("var " + idCorreo);
    var params = { "idCorreo": idCorreo };
    $.ajax({
        data: params,
        url: '../controller/route/configuracion/correoElegido.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#smg_panel").html(response); }
    });
});
/****************************************************************************/
/*      AJAX modulo de Usuario                                              */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/

/*Opcion para UI de perfil del usuario actual*/
$(document).on('click', '#perfil', function(e) {
    console.log("Opcion -> perfil");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/usuarios/perfil.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contendor").html(response);
        }
    });
});
/*************************************************/
function mostrarErrorLogin(titulo, mensaje) {
    $("#loginErrorTitle").text(titulo || "No pudimos iniciar sesion");
    $("#loginErrorMessage").text(mensaje || "Revisa los datos ingresados e intenta nuevamente.");
    $("#loginErrorModal").addClass("is-visible");
}

$(document).on('click', '#cerrarLoginError', function(e) {
    e.preventDefault();
    $("#loginErrorModal").removeClass("is-visible");
});

$(document).on('click', '#loginErrorModal', function(e) {
    if (e.target === this) {
        $("#loginErrorModal").removeClass("is-visible");
    }
});

$(document).on('click', '#validarSesion', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    var user = document.getElementById("usuario").value;
    var pass = document.getElementById("clave").value;

    if (user.trim().length === 0 || pass.length === 0) {
        mostrarErrorLogin(
            "Datos incompletos",
            "Ingresa tu usuario y contrasena para continuar."
        );
        return;
    }

    var params = { "usuario": user, "clave": pass };
    $.ajax({
        data: params,
        url: '../controller/route/usuarios/validarSesion.php',
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            $("#smg_login").html("");
        },
        success: function(response) {
            if (response && response.estado === "ok" && response.url) {
                window.location.replace(response.url);
                return;
            }

            mostrarErrorLogin(
                response && response.titulo ? response.titulo : "No pudimos iniciar sesion",
                response && response.mensaje ? response.mensaje : "El usuario o la contrasena no son correctos."
            );
        },
        error: function() {
            mostrarErrorLogin(
                "No se pudo validar el acceso",
                "El servidor no respondio correctamente. Espera un momento e intenta de nuevo."
            );
        }
    });
});
/***************************************************************/

$(document).on('click', '#cerrarSesion', function(e) {
    e.preventDefault();
    window.location.replace($(this).attr('href'));
});

/*************************************************/
$(document).on('click', '#cambioContraseña', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("Opcion -> cambio de contraseña");
    $("#smg_panel").html("");
    var actual = document.getElementById("actual").value;
    var nueva = document.getElementById("nueva").value;
    var params = { "claveActual": actual, "claveNueva": nueva };
    console.log("var " + actual + " " + nueva);

    $.ajax({
        data: params,
        url: '../controller/route/usuarios/cambioClave.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#smg_panel").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#verificarUsuario', function(e) {
    console.log("Opcion -> verificar usuario");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    var usuario = document.getElementById("user").value;
    console.log("var " + usuario);
    var params = { "user": usuario };
    $.ajax({
        data: params,
        url: '../controller/route/usuarios/verificarUsuario.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#smg_verificar").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#crearUsuario', function(e) {
    console.log("Opcion -> crear usuario");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");

    var check = 0;
    var usuario = document.getElementById("usuario").value;
    if (usuario.length == 0 || usuario == 'REQUERIDO') {
        check++;
        document.getElementById("usuario").value = 'REQUERIDO';
    }

    var clave = document.getElementById("clave").value;
    if (clave.length == 0 || clave == 'REQUERIDO') {
        check++;
        document.getElementById("clave").value = 'REQUERIDO';
    }

    var idRol = document.getElementById("idRol").value;

    console.log("var " +
        idRol + " " +
        usuario + " " +
        clave);
    if (parseInt(check) > 0) {
        alert('No se puede registrar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {


        var params = {
            "idRol": idRol,
            "usuario": usuario,
            "clave": clave
        };
        $.ajax({
            data: params,
            url: '../controller/route/usuarios/crearUsuario.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_usuario").html(response);
            }
        });
        check = 0;
    }
});
/*************************************************/
$(document).on('click', '#ModificarUsuario', function(e) {
    console.log("Opcion -> modificar usuario");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");


    var check = 0;
    var usuario = document.getElementById("usuario").value;
    if (usuario.length == 0 || usuario == 'REQUERIDO') {
        check++;
        document.getElementById("usuario").value = 'REQUERIDO';
    }

    var clave = document.getElementById("clave").value;
    if (clave.length == 0 || clave == 'REQUERIDO') {
        check++;
        document.getElementById("clave").value = 'REQUERIDO';
    }

    var idRol = document.getElementById("idRol").value;
    console.log("var " +
        idRol + " " +
        usuario + " " +
        clave);
    if (parseInt(check) > 0) {
        alert('No se puede registrar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {


        $.ajax({
            data: params,
            url: '../controller/route/usuarios/modificarUsuario.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_usuario").html(response);
            }
        });
        check = 0;
    }
});
/*************************************************/
$(document).on('click', '#desactivarUsuario', function(e) {
    console.log("Opcion -> desactivar usuario");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    var idUsuario = document.getElementById("idUsuario").value;
    console.log("var " + idUsuario);
    var params = { "idUsuario": idUsuario };
    $.ajax({
        data: params,
        url: '../controller/route/usuarios/desactivarUsuario.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#smg_panel").html(response); }
    });
});
/****************************************************************************/
/*      AJAX modulo de empleado                                             */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
/*Opcion para UI de opcion menu principal empleado*/
$(document).on('click', '#empleadoMain', function(e) {
    console.log("Opcion -> empleado menu principal");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/empleados/empleadoMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
        }
    });
});
/*************************************************/
/*Opcion para UI de opcion formulario crear empleado*/
$(document).on('click', '#nuevoEmpleado', function(e) {
    console.log("Opcion -> empleado formulario registro empleado ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/empleados/nuevoEmpleado.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
        }
    });
});
/*************************************************/
/*Opcion para UI de opcion ver informacion del  empleado*/
$(document).on('click', '#idEmpleado', function(e) {
    console.log("Opcion -> ver empleado");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var idEmpleado = $(this).attr('title');
    console.log("var " + idEmpleado);
    var params = { "id": idEmpleado };
    $.ajax({
        data: params,
        url: 'layout/empleados/verEmpleado.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#crearEmpleado', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("Opcion -> boton crear Empleado");
    $("#smg_panel").html("");


    var check = 0; //var de validacion
    var nombre = document.getElementById("nombre").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("nombre").value = 'REQUERIDO';
    }

    var tipo_documento = document.getElementById("tipo_documento").value;

    var numero_documento = document.getElementById("numero_documento").value;
    if (numero_documento.length == 0 || numero_documento == 'REQUERIDO') {
        check++;
        document.getElementById("numero_documento").value = 'REQUERIDO';
    }

    var direccion = document.getElementById("direccion").value;
    if (direccion.length == 0 || direccion == 'REQUERIDO') {
        check++;
        document.getElementById("direccion").value = 'REQUERIDO';
    }

    var ciudad = document.getElementById("ciudad").value;
    if (ciudad.length == 0 || ciudad == 'REQUERIDO') {
        check++;
        document.getElementById("ciudad").value = 'REQUERIDO';
    }

    var fijo = document.getElementById("fijo").value;
    if (fijo.length == 0) { fijo = ""; }

    var celular1 = document.getElementById("celular1").value;
    if (celular1.length == 0 || celular1 == 'REQUERIDO') {
        check++;
        document.getElementById("celular1").value = 'REQUERIDO';
    }

    var celular2 = document.getElementById("celular2").value;
    if (celular2.length == 0) { celular2 = ""; }

    var celular3 = document.getElementById("celular3").value;
    if (celular3.length == 0) { celular3 = ""; }

    var email = document.getElementById("email").value;
    if (email.length == 0 || email == 'REQUERIDO') {
        check++;
        document.getElementById("email").value = 'REQUERIDO';
    }

    var foto = document.getElementById("foto").value;
    if (foto.length == 0) { foto = "../assets/images/random-avatar4.jpg"; }

    var usuario = document.getElementById("user").value;
    if (usuario.length == 0 || usuario == 'REQUERIDO') {
        check++;
        document.getElementById("user").value = 'REQUERIDO';
    }

    var contrasena = document.getElementById("password").value;
    if (contrasena.length == 0 || contrasena == 'REQUERIDO') {
        check++;
        document.getElementById("password").value = 'REQUERIDO';
    }
    var Rol = document.getElementById("rol").value;

    console.log("var " + " " + nombre + " " +
        tipo_documento + " " +
        numero_documento + " " +
        direccion + " " +
        ciudad + " " +
        fijo + " " +
        celular1 + " " +
        celular2 + " " +
        celular3 + " " +
        email + " " +
        foto + " " +
        usuario + " " +
        contrasena + " " +
        Rol);
    if (parseInt(check) > 0) {
        alert('No se puede registrar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        var params = {
            "nombre": nombre,
            "tipo_documento": tipo_documento,
            "numero_documento": numero_documento,
            "direccion": direccion,
            "ciudad": ciudad,
            "fijo": fijo,
            "celular1": celular1,
            "celular2": celular2,
            "celular3": celular3,
            "email": email,
            "foto": foto,
            "user": usuario,
            "password": contrasena,
            "rol": Rol
        };
        $.ajax({
            data: params,
            url: '../controller/route/empleados/crearEmpleado.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_empleado").html(response);
            }
        });
        check = 0;
    }

});
/*************************************************/
$(document).on('click', '#ModificarEmpleado', function(e) {
    console.log("Opcion -> boton modificar empleado");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");


    var check = 0; //var de validacion
    var id_empleado = document.getElementById("id_empleado").value;
    var id_usuario = document.getElementById("id_usuario").value;
    var nombre = document.getElementById("nombre").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("nombre").value = 'REQUERIDO';
    }

    var tipo_documento = document.getElementById("tipo_documento").value;

    var numero_documento = document.getElementById("numero_documento").value;
    if (numero_documento.length == 0 || numero_documento == 'REQUERIDO') {
        check++;
        document.getElementById("numero_documento").value = 'REQUERIDO';
    }

    var direccion = document.getElementById("direccion").value;
    if (direccion.length == 0 || direccion == 'REQUERIDO') {
        check++;
        document.getElementById("direccion").value = 'REQUERIDO';
    }

    var ciudad = document.getElementById("ciudad").value;
    if (ciudad.length == 0 || ciudad == 'REQUERIDO') {
        check++;
        document.getElementById("ciudad").value = 'REQUERIDO';
    }

    var fijo = document.getElementById("fijo").value;
    if (fijo.length == 0) { fijo = ""; }

    var celular1 = document.getElementById("celular1").value;
    if (celular1.length == 0 || celular1 == 'REQUERIDO') {
        check++;
        document.getElementById("celular1").value = 'REQUERIDO';
    }

    var celular2 = document.getElementById("celular2").value;
    if (celular2.length == 0) { celular2 = ""; }

    var celular3 = document.getElementById("celular3").value;
    if (celular3.length == 0) { celular3 = ""; }

    var email = document.getElementById("email").value;
    if (email.length == 0 || email == 'REQUERIDO') {
        check++;
        document.getElementById("email").value = 'REQUERIDO';
    }

    var foto = document.getElementById("foto").value;
    if (foto.length == 0) { foto = "../assets/images/random-avatar4.jpg"; }

    var usuario = document.getElementById("user").value;
    if (usuario.length == 0 || usuario == 'REQUERIDO') {
        check++;
        document.getElementById("user").value = 'REQUERIDO';
    }

    var contrasena = document.getElementById("password").value;
    if (contrasena.length == 0 || contrasena == 'REQUERIDO') {
        check++;
        document.getElementById("password").value = 'REQUERIDO';
    }
    var Rol = document.getElementById("rol").value;


    console.log("var " +
        id_empleado + " " +
        id_usuario + " " +
        nombre + " " +
        tipo_documento + " " +
        numero_documento + " " +
        direccion + " " +
        ciudad + " " +
        fijo + " " +
        celular1 + " " +
        celular2 + " " +
        celular3 + " " +
        email + " " +
        foto + " " +
        usuario + " " +
        contrasena + " " +
        Rol);


    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "id_empleado": id_empleado,
            "id_usuario": id_usuario,
            "nombre": nombre,
            "tipo_documento": tipo_documento,
            "numero_documento": numero_documento,
            "direccion": direccion,
            "ciudad": ciudad,
            "fijo": fijo,
            "celular1": celular1,
            "celular2": celular2,
            "celular3": celular3,
            "email": email,
            "foto": foto,
            "user": usuario,
            "password": contrasena,
            "rol": Rol
        };
        $.ajax({
            data: params,
            url: '../controller/route/empleados/modificarEmpleado.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_empleado").html(response);
            }
        });
        check = 0;
    }
});
/****************************************************************************/
/*      AJAX modulo de cliente                                             */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/

/*Opcion para UI de opcion menu principal empleado*/
$(document).on('click', '#clienteMain', function(e) {
    console.log("Opcion -> cliente menu principal");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/clientes/clienteMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
        }
    });
});
/*************************************************/
/*Opcion para UI de opcion formulario crear cliente*/
$(document).on('click', '#nuevoCliente', function(e) {
    console.log("Opcion -> nuevo cliente ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/clientes/nuevoCliente.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
        }
    });
});
/*************************************************/
/*Opcion para UI de opcion ver informacion del  empleado*/
$(document).on('click', '#idCliente', function(e) {
    console.log("Opcion -> ver cliente");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var idCliente = $(this).attr('title');
    console.log("var " + idCliente);
    var params = { "id": idCliente };
    $.ajax({
        data: params,
        url: 'layout/clientes/verCliente.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#crearCliente', function(e) {
    console.log("Opcion -> boton crear Cliente");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");



    var check = 0;

    var social = document.getElementById("cliente_social").value;
    if (social.length == 0 || social == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_social").value = 'REQUERIDO';
    }

    var nit = document.getElementById("cliente_nit").value;
    if (nit.length == 0 || nit == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_nit").value = 'REQUERIDO';
    }

    var contacto = document.getElementById("cliente_nombre_contacto").value;
    if (contacto.length == 0 || contacto == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_nombre_contacto").value = 'REQUERIDO';
    }

    var direccion = document.getElementById("cliente_direccion").value;
    if (direccion.length == 0 || direccion == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_direccion").value = 'REQUERIDO';
    }

    var ciudad = document.getElementById("cliente_ciudad").value;
    if (ciudad.length == 0 || ciudad == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_ciudad").value = 'REQUERIDO';
    }

    var telefono1 = document.getElementById("cliente_telefono1").value;
    if (telefono1.length == 0 || telefono1 == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_telefono1").value = 'REQUERIDO';
    }

    var telefono2 = document.getElementById("cliente_telefono2").value;
    if (telefono2.length == 0) { telefono2 = ""; }

    var telefono3 = document.getElementById("cliente_telefono3").value;
    if (telefono3.length == 0) { telefono3 = ""; }

    var email1 = document.getElementById("cliente_email1").value;
    if (email1.length == 0 || email1 == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_email1").value = 'REQUERIDO';
    }

    var email2 = document.getElementById("cliente_email2").value;
    if (email2.length == 0) { email2 = ""; }

    var email3 = document.getElementById("cliente_email3").value;
    if (email3.length == 0) { email3 = ""; }

    var email4 = document.getElementById("cliente_email4").value;
    if (email4.length == 0) { email4 = ""; }

    var email5 = document.getElementById("cliente_email5").value;
    if (email5.length == 0) { email5 = ""; }

    var email6 = document.getElementById("cliente_email6").value;
    if (email6.length == 0) { email6 = ""; }

    var email7 = document.getElementById("cliente_email7").value;
    if (email7.length == 0) { email7 = ""; }

    var email8 = document.getElementById("cliente_email8").value;
    if (email8.length == 0) { email8 = ""; }

    var foto = document.getElementById("cliente_foto").value;
    if (foto.length == 0) { foto = "../assets/images/random-avatar4.jpg"; }

    var usuario = document.getElementById("user").value;
    if (usuario.length == 0 || usuario == 'REQUERIDO') {
        check++;
        document.getElementById("user").value = 'REQUERIDO';
    }

    var contrasena = document.getElementById("password").value;
    if (contrasena.length == 0 || contrasena == 'REQUERIDO') {
        check++;
        document.getElementById("password").value = 'REQUERIDO';
    }

    console.log("var " + " " +
        usuario + " " +
        contrasena + " " +
        social + " " +
        nit + " " +
        contacto + " " +
        direccion + " " +
        ciudad + " " +
        telefono1 + " " +
        telefono2 + " " +
        telefono3 + " " +
        email1 + " " +
        email2 + " " +
        email3 + " " +
        email4 + " " +
        email5 + " " +
        email6 + " " +
        email7 + " " +
        email8 + " " + foto);
    if (parseInt(check) > 0) {
        alert('No se puede registrar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "cliente_social": social,
            "cliente_nit": nit,
            "cliente_nombre_contacto": contacto,
            "cliente_direccion": direccion,
            "cliente_ciudad": ciudad,
            "cliente_telefono1": telefono1,
            "cliente_telefono2": telefono2,
            "cliente_telefono3": telefono3,
            "cliente_email1": email1,
            "cliente_email2": email2,
            "cliente_email3": email3,
            "cliente_email4": email4,
            "cliente_email5": email5,
            "clienta_email6": email6,
            "cliente_email7": email7,
            "cliente_email8": email8,
            "cliente_foto": foto,
            "user": usuario,
            "password": contrasena
        };
        $.ajax({
            data: params,
            url: '../controller/route/clientes/crearCliente.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_cliente").html(response);
            }
        });
        check = 0;
    }
});
/*************************************************/
$(document).on('click', '#ModificarCliente', function(e) {
    console.log("Opcion -> boton modificar cliente");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");

    var check = 0; //var de validacion
    var id_cliente = document.getElementById("id_cliente").value;
    var id = document.getElementById("id_usuario").value;
    var social = document.getElementById("cliente_social").value;
    if (social.length == 0 || social == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_social").value = 'REQUERIDO';
    }

    var nit = document.getElementById("cliente_nit").value;
    if (nit.length == 0 || nit == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_nit").value = 'REQUERIDO';
    }

    var contacto = document.getElementById("cliente_nombre_contacto").value;
    if (contacto.length == 0 || contacto == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_nombre_contacto").value = 'REQUERIDO';
    }

    var direccion = document.getElementById("cliente_direccion").value;
    if (direccion.length == 0 || direccion == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_direccion").value = 'REQUERIDO';
    }

    var ciudad = document.getElementById("cliente_ciudad").value;
    if (ciudad.length == 0 || ciudad == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_ciudad").value = 'REQUERIDO';
    }

    var telefono1 = document.getElementById("cliente_telefono1").value;
    if (telefono1.length == 0 || telefono1 == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_telefono1").value = 'REQUERIDO';
    }

    var telefono2 = document.getElementById("cliente_telefono2").value;
    if (telefono2.length == 0) { telefono2 = ""; }

    var telefono3 = document.getElementById("cliente_telefono3").value;
    if (telefono3.length == 0) { telefono3 = ""; }

    var email1 = document.getElementById("cliente_email1").value;
    if (email1.length == 0 || email1 == 'REQUERIDO') {
        check++;
        document.getElementById("cliente_email1").value = 'REQUERIDO';
    }

    var email2 = document.getElementById("cliente_email2").value;
    if (email2.length == 0) { email2 = ""; }

    var email3 = document.getElementById("cliente_email3").value;
    if (email3.length == 0) { email3 = ""; }

    var email4 = document.getElementById("cliente_email4").value;
    if (email4.length == 0) { email4 = ""; }

    var email5 = document.getElementById("cliente_email5").value;
    if (email5.length == 0) { email5 = ""; }

    var email6 = document.getElementById("cliente_email6").value;
    if (email6.length == 0) { email6 = ""; }

    var email7 = document.getElementById("cliente_email7").value;
    if (email7.length == 0) { email7 = ""; }

    var email8 = document.getElementById("cliente_email8").value;
    if (email8.length == 0) { email8 = ""; }

    var foto = document.getElementById("cliente_foto").value;
    if (foto.length == 0) { foto = "../assets/images/random-avatar4.jpg"; }

    var usuario = document.getElementById("user").value;
    if (usuario.length == 0 || usuario == 'REQUERIDO') {
        check++;
        document.getElementById("user").value = 'REQUERIDO';
    }

    var contrasena = document.getElementById("password").value;
    if (contrasena.length == 0 || contrasena == 'REQUERIDO') {
        check++;
        document.getElementById("password").value = 'REQUERIDO';
    }

    console.log("var " + " " +
        id_cliente + " " +
        id + " " +
        social + " " +
        nit + " " +
        contacto + " " +
        direccion + " " +
        ciudad + " " +
        telefono1 + " " +
        telefono2 + " " +
        telefono3 + " " +
        email1 + " " +
        email2 + " " +
        email3 + " " +
        email4 + " " +
        email5 + " " +
        email6 + " " +
        email7 + " " +
        email8 + " " +
        usuario + " " +
        contrasena);
    if (parseInt(check) > 0) {
        alert('No se puede registrar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        var params = {
            "id_cliente": id_cliente,
            "id_usuario": id,
            "cliente_social": social,
            "cliente_nit": nit,
            "cliente_nombre_contacto": contacto,
            "cliente_direccion": direccion,
            "cliente_ciudad": ciudad,
            "cliente_telefono1": telefono1,
            "cliente_telefono2": telefono2,
            "cliente_telefono3": telefono3,
            "cliente_email1": email1,
            "cliente_email2": email2,
            "cliente_email3": email3,
            "cliente_email4": email4,
            "cliente_email5": email5,
            "clienta_email6": email6,
            "cliente_email7": email7,
            "cliente_email8": email8,
            "cliente_foto": foto,
            "user": usuario,
            "password": contrasena
        };
        $.ajax({
            data: params,
            url: '../controller/route/clientes/modificarCliente.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_cliente").html(response);
            }
        });
        check = 0;
    }
});
/****************************************************************************/

$(document).on('click', '#buscarClienteRazon', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var RazonSocial = document.getElementById("RazonSocial").value;
    if (RazonSocial.length == 0 || RazonSocial == 'REQUERIDO') {
        check++;
        document.getElementById("RazonSocial").value = 'REQUERIDO';
    }

    console.log("var " + RazonSocial);

    if (parseInt(check) > 0) {
        alert('No se puede buscar puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = { "RazonSocial": RazonSocial };
        $.ajax({
            data: params,
            url: '../controller/route/clientes/buscarClienteRazon.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablacliente").html(response);
            }
        });
        check = 0;
    }
});
/************************************************************************/
$(document).on('click', '#buscarCliente_', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var nombre = document.getElementById("nombre").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("nombre").value = 'REQUERIDO';
    }

    console.log("var " + nombre);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;
        var params = { "nombre": nombre };
        var ContentResponse = '#tablacliente';
        console.log('aqui');
        $.ajax({

            data: params,
            url: '../controller/route/clientes/buscarCliente.php',
            type: 'POST',
            dataType: 'html',
            beforeSend: function() {
                $(ContentResponse).html("Cargando espere por favor");
            },
            success: function(response) {
                $(ContentResponse).html(response);
            },
            error: function() { console.log('error '); },
            complete: function() {}

        }).fail(function(jqXHR, textStatus, errorThrown) {

            if (jqXHR.status === 0) {
                $(ContentResponse).html('Sin conexion: Verifica tu conexion.');
            } else if (jqXHR.status == 404) {
                $(ContentResponse).html('Página solicitada no encontrada[404].');
            } else if (jqXHR.status == 500) {
                $(ContentResponse).html('Error interno del servidor [500].');
            } else if (textStatus === 'parsererror') {
                $(ContentResponse).html('Solicitud de archivo json fallida o error de parser');
            } else if (textStatus === 'timeout') {
                $(ContentResponse).html('Error de tiempo de espera.');
            } else if (textStatus === 'abort') {
                $(ContentResponse).html('Petición abortada..');
            } else {
                $(ContentResponse).html('Error no detectado : ' + jqXHR.responseText);
            }
        });
    }



});
/****************************************************************************/
/*      AJAX modulo de Conductor                                            */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
$(document).on('click', '#conductorMain', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu conductor");
    $.ajax({
        url: 'layout/conductor/conductorMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/**************************************************************************/
$(document).on('click', '#nuevoConductor', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> nuevo conductor");
    $.ajax({
        url: 'layout/conductor/nuevoConductor.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/******************************************************************************/
$(document).on('click', '#idConductor', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("Opcion -> ver conductor");
    $("#smg_panel").html("");
    $("#contenido").html("");
    var idConductor = $(this).attr('title');
    console.log("var " + idConductor);
    var params = { "id": idConductor };
    $.ajax({
        data: params,
        url: 'layout/conductor/verConductor.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

/*******************************************************************************/
$(document).on('click', '#crearConductor', function(e) {
    console.log("Opcion -> crear conductor");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");


    var check = 0; //var de validacion
    var cedula = document.getElementById("ccConductor").value;
    if (cedula.length == 0 || cedula == 'REQUERIDO') {
        check++;
        document.getElementById("ccConductor").value = 'REQUERIDO';
    }

    var nombre_c = document.getElementById("nameConductor").value;
    if (nombre_c.length == 0 || nombre_c == 'REQUERIDO') {
        check++;
        document.getElementById("nameConductor").value = 'REQUERIDO';
    }

    var telefono1 = document.getElementById("telefono1").value;
    if (telefono1.length == 0 || telefono1 == 'REQUERIDO') {
        check++;
        document.getElementById("telefono1").value = 'REQUERIDO';
    }

    var telefono2 = document.getElementById("telefono2").value;
    if (telefono2.length == 0) { telefono2 = ""; }

    var telefono3 = document.getElementById("telefono3").value;
    if (telefono3.length == 0) { telefono3 = ""; }

    console.log("var " +
        cedula + " " +
        nombre_c + " " +
        telefono1 + " " +
        telefono2 + " " +
        telefono3);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "ccConductor": cedula,
            "nameConductor": nombre_c,
            "telefono1": telefono1,
            "telefono2": telefono2,
            "telefono3": telefono3
        };
        $.ajax({
            data: params,
            url: '../controller/route/conductor/crearConductor.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_conductor").html(response);
            }
        });
        check = 0;
    }
});
/*******************************************************************************/
$(document).on('click', '#editarConductor', function(e) {
    console.log("Opcion -> editar conductor");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");



    var idConductor = document.getElementById("idConductor").value;
    var check = 0; //var de validacion

    var cedula = document.getElementById("ccConductor").value;
    if (cedula.length == 0 || cedula == 'REQUERIDO') {
        check++;
        document.getElementById("ccConductor").value = 'REQUERIDO';
    }

    var nombre_c = document.getElementById("nameConductor").value;
    if (nombre_c.length == 0 || nombre_c == 'REQUERIDO') {
        check++;
        document.getElementById("nameConductor").value = 'REQUERIDO';
    }

    var telefono1 = document.getElementById("telefono1").value;
    if (telefono1.length == 0 || telefono1 == 'REQUERIDO') {
        check++;
        document.getElementById("telefono1").value = 'REQUERIDO';
    }

    var telefono2 = document.getElementById("telefono2").value;
    if (telefono2.length == 0) { telefono2 = ""; }

    var telefono3 = document.getElementById("telefono3").value;
    if (telefono3.length == 0) { telefono3 = ""; }

    console.log("var " +
        idConductor + " " +
        cedula + " " +
        nombre_c + " " +
        telefono1 + " " +
        telefono2 + " " +
        telefono3);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "idConductor": idConductor,
            "ccConductor": cedula,
            "nameConductor": nombre_c,
            "telefono1": telefono1,
            "telefono2": telefono2,
            "telefono3": telefono3
        };
        $.ajax({
            data: params,
            url: '../controller/route/conductor/editarConductor.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_conductor").html(response);
            }
        });
        check = 0;
    }
});

/************************************************************************/
$(document).on('click', '#buscarConductor', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var nombre = document.getElementById("nombre").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("nombre").value = 'REQUERIDO';
    }

    console.log("var " + nombre);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;
        var params = { "nombre": nombre };
        $.ajax({
            data: params,
            url: '../controller/route/conductor/buscarConductor.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablaconductor").html(response);
            }
        });
    }
});
/*******************************************************************************/
$(document).on('click', '#buscarConductorNombre', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var Nombre = document.getElementById("Nombre").value;
    if (Nombre.length == 0 || Nombre == 'REQUERIDO') {
        check++;
        document.getElementById("Nombre").value = 'REQUERIDO';
    }

    console.log("var " + Nombre);

    if (parseInt(check) > 0) {
        alert('No se puede buscar puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = { "Nombre": Nombre };
        $.ajax({
            data: params,
            url: '../controller/route/conductor/buscarConductorNombre.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablaconductor").html(response);
            }
        });
        check = 0;
    }
});
/****************************************************************************/
/*      AJAX modulo de Vehiculo                                             */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
$(document).on('click', '#vehiculoMain', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu vehiculo");
    $.ajax({
        url: 'layout/vehiculo/vehiculoMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*********************************************************************************/
$(document).on('click', '#nuevoVehiculo', function(e) {
    console.log("Opcion -> crear nuevo vehiculo");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/vehiculo/nuevoVehiculo.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/****************************************************************************/
$(document).on('click', '#buscarVehiculo', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var nombre = document.getElementById("busquedadVehiculo").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("busquedadVehiculo").value = 'REQUERIDO';
    }

    console.log("var " + nombre);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;
        var params = { "nombre": nombre };
        $.ajax({
            data: params,
            url: '../controller/route/vehiculo/buscarVehiculo.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablavehiculo").html(response);
            }
        });
    }
});
/*******************************************************************************/
$(document).on('click', '#buscarVehiculoPlaca', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var Placa = document.getElementById("Placa").value;
    if (Placa.length == 0 || Placa == 'REQUERIDO') {
        check++;
        document.getElementById("Placa").value = 'REQUERIDO';
    }

    console.log("var " + Placa);

    if (parseInt(check) > 0) {
        alert('No se puede buscar puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = { "Placa": Placa };
        $.ajax({
            data: params,
            url: '../controller/route/vehiculo/buscarVehiculoPlaca.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablavehiculo").html(response);
            }
        });
        check = 0;
    }
});
/*******************************************************************************/
$(document).on('click', '#idVehiculo', function(e) {
    console.log("Opcion -> ver vehiculo");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var idVehiculo = $(this).attr('title');
    console.log("var " + idVehiculo);
    var params = { "id": idVehiculo };
    $.ajax({
        data: params,
        url: 'layout/vehiculo/verVehiculo.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*******************************************************************************/
$(document).on('click', '#crearVehiculo', function(e) {
    console.log("Opcion -> crear vehiculo");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");



    var check = 0; //var de validacion
    var id_cliente = document.getElementById("id_cliente").value;
    if (id_cliente.length == 0) { id_cliente = ""; }

    var placa = document.getElementById("Placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("Placa").value = 'REQUERIDO';
    }

    var marca = document.getElementById("Marca").value;
    if (marca.length == 0 || marca == 'REQUERIDO') {
        check++;
        document.getElementById("Marca").value = 'REQUERIDO';
    }

    var color = document.getElementById("Color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("Color").value = 'REQUERIDO';
    }

    var n_trailer = document.getElementById("trailer").value;
    if (n_trailer.length == 0) { n_trailer = ""; }

    console.log("var" +
        id_cliente + " " +
        placa + " " +
        marca + " " +
        color + " " +
        n_trailer);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "id_cliente": id_cliente,
            "Placa": placa,
            "Marca": marca,
            "Color": color,
            "trailer": n_trailer
        };
        $.ajax({
            data: params,
            url: '../controller/route/vehiculo/crearVehiculo.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_vehiculo").html(response);
            }
        });
        check = 0;
    }
});

/****************************************************************************/
$(document).on('click', '#editarVehiculo', function(e) {
    console.log("Opcion -> editar vehiculo");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");



    var check = 0; //var de validacion

    var id_vehiculo = document.getElementById("id_vehiculo").value;
    var id_cliente = 1;
    if (id_cliente.length == 0) { id_cliente = ""; }

    var placa = document.getElementById("Placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("Placa").value = 'REQUERIDO';
    }

    var marca = document.getElementById("Marca").value;
    if (marca.length == 0 || marca == 'REQUERIDO') {
        check++;
        document.getElementById("Marca").value = 'REQUERIDO';
    }

    var color = document.getElementById("Color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("Color").value = 'REQUERIDO';
    }

    var n_trailer = document.getElementById("trailer").value;
    if (n_trailer.length == 0) { n_trailer = ""; }

    console.log("var" +
        id_vehiculo + " " +
        id_cliente + " " +
        placa + " " +
        marca + " " +
        color + " " +
        n_trailer);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        var params = {
            "id_vehiculo": id_vehiculo,
            "id_cliente": id_cliente,
            "Placa": placa,
            "Marca": marca,
            "Color": color,
            "trailer": n_trailer
        };
        $.ajax({
            data: params,
            url: '../controller/route/vehiculo/editarVehiculo.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_vehiculo").html(response);
            }
        });
        check = 0;
    }
});
/****************************************************************************/
/*      AJAX modulo de servicios                                            */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.image-upload-wrap').hide();
            $('.file-upload-image').attr('src', e.target.result);
            $('.file-upload-content').show();
            $('.image-title').html(input.files[0].name);
        };
        reader.readAsDataURL(input.files[0]);
    } else { removeUpload(); }
}

function readURL2(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.image-upload-wrap2').hide();
            $('.file-upload-image2').attr('src', e.target.result);
            $('.file-upload-content2').show();
            $('.image-title2').html(input.files[0].name);
        };
        reader.readAsDataURL(input.files[0]);
    } else { removeUpload2(); }
}

function removeUpload() {
    $('.file-upload-input').replaceWith($('.file-upload-input').clone());
    $('.file-upload-content').hide();
    $('.image-upload-wrap').show();
}
$('.image-upload-wrap').bind('dragover', function() {
    $('.image-upload-wrap').addClass('image-dropping');
});
$('.image-upload-wrap').bind('dragleave', function() {
    $('.image-upload-wrap').removeClass('image-dropping');
});


function removeUpload2() {
    $('.file-upload-input2').replaceWith($('.file-upload-input2').clone());
    $('.file-upload-content2').hide();
    $('.image-upload-wrap2').show();
}
$('.image-upload-wrap2').bind('dragover', function() {
    $('.image-upload-wrap2').addClass('image-dropping');
});
$('.image-upload-wrap2').bind('dragleave', function() {
    $('.image-upload-wrap2').removeClass('image-dropping');
});
/********************************************************************/
/*funcion de subido para imagenes,email y archivos */


/***********************************************************************/
$(document).on('click', '#nuevaMinuta', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("opcion -> crear nueva minuta  ");

    var ide = document.getElementById("idempleadoHidden").value;
    var ids = document.getElementById("idservicioHidden").value;
    console.log("var " + ids + " --- " + ide);
    var params = { "ids": ids, "ide": ide };
    $.ajax({
        data: params,
        url: 'layout/servicios/nuevaMinuta.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#nuevoServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("opcion -> crear nuevo servicio  ");
    $.ajax({
        url: 'layout/servicios/nuevoServicio.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/

$(document).on('click', '#historialServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();
    var verServicioEstado = $(this).attr('title');
    console.log("var " + verServicioEstado);
    var params = { "id": verServicioEstado };
    $.ajax({
        data: params,
        url: 'layout/servicios/historiaServicio.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*********************************************************************/
function abrirBitacoraServicio(id) {
    console.log("opcion -> servicios -> bitacora  ");
    console.log("var " + id);
    var params = { "id": id };
    $.ajax({
        data: params,
        url: 'layout/servicios/bitacora.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
}

$(document).on('click', '#bitacora', function(e) {
    e.stopPropagation();
    e.preventDefault();
    abrirBitacoraServicio($(this).attr('title'));
});

$(document).on('click', '#volver_verMinuta_bitacora', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("opcion -> volver  ");
    var id = document.getElementById("volver").value;
    console.log("var " + id);
    var params = { "id": id };
    $.ajax({
        data: params,
        url: 'layout/servicios/bitacora.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************************/
// $(document).on('click','#buscarCliente',function(e){
//   e.stopPropagation();
//   e.preventDefault();
//   var verServicioEstado=$(this).attr('title');
//   console.log("var "+verServicioEstado);
//   var params = { "id" : verServicioEstado};
//   $.ajax({
//     url:   'layout/servicios/historiaServicio.php',
//     type:  'post',
//     beforeSend: function () {},
//     success:  function (response) {$("#contenido").html(response);}});
//   });
/*************************************************/
$(document).on('click', '#listaServicios', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("opcion -> Servicios Activos ");
    $.ajax({
        url: 'layout/servicios/serviciosActivos.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#consultaServiciosCliente', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("opcion -> Servicios Activos ");
    $.ajax({
        url: 'layout/servicios/historiaServicio.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*************************************************/
$(document).on('click', '#buscarCliente', function(e) {
    e.stopPropagation();
    e.preventDefault();


    check = 0;
    var idservicio = document.getElementById("buscarCliente").value;

    var fechai = document.getElementById("fechai").value;
    if (fechai.length == 0 || fechai == 'REQUERIDO') {
        check++;
        document.getElementById("fechai").value = 'REQUERIDO';
    }

    var fechaf = document.getElementById("fechaf").value;
    if (fechaf.length == 0 || fechaf == 'REQUERIDO') {
        check++;
        document.getElementById("fechaf").value = 'REQUERIDO';
    }

    console.log("var " + idservicio) + " " + fechai + " " + fechaf;
    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;
        var params = {
            "idservicio": idservicio,
            "fechai": fechai,
            "fechaf": fechaf
        };
        $.ajax({
            data: params,
            url: '../controller/route/servicios/buscarCliente.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) { $("#tabla").html(response); }
        });
    }

});
/****************************************************************************/
$(document).on('click', '#buscarClientePlaca', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    console.log("opcion -> Busqueda cliente placa");
    var idcliente = document.getElementById("idcliente").value;
    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    console.log("var " + placa + " " + idcliente);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;

        var params = {
            "placa": placa,
            "idcliente": idcliente
        };
        $.ajax({
            data: params,
            url: '../controller/route/servicios/buscarClientePlaca.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablan").html(response);

            }
        });
    }

});
/****************************************************************************/
$(document).on('click', '#buscarClienteNombre', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;

    var idservicio = document.getElementById("busquedaHistorial").value;
    if (idservicio.length == 0 || idservicio == 'REQUERIDO') {
        check++;
        document.getElementById("busquedaHistorial").value = 'REQUERIDO';
    }

    // var fechai = document.getElementById("fechai").value;
    // if (fechai.length == 0 || fechai == 'REQUERIDO') {
    //     check++;
    //     document.getElementById("fechai").value = 'REQUERIDO';
    // }

    // var fechaf = document.getElementById("fechaf").value;
    // if (fechaf.length == 0 || fechaf == 'REQUERIDO') {
    //     check++;
    //     document.getElementById("fechaf").value = 'REQUERIDO';
    // }

    // fechai = fechai + " 00:00";
    // fechaf = fechaf + " 00:00";
    // console.log("var " + idservicio + " " + fechai + " " + fechaf);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;

        var params = {
            "busquedaHistorial": idservicio
        };
        $.ajax({
            data: params,
            url: '../controller/route/servicios/buscarCliente.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablan").html(response);

            }
        });
    }

});
/**************************************************************************/
$(document).on('click', '#buscarEmpleadoServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("paso de datos consulta empleado con sus servicios registrados");
    check = 0;
    var idservicio = document.getElementById("busquedaHistorial").value;
    if (idservicio.length == 0 || idservicio == 'REQUERIDO') {
        check++;
        document.getElementById("busquedaHistorial").value = 'REQUERIDO';
    }

    var fechai = document.getElementById("fechai").value;
    if (fechai.length == 0 || fechai == 'REQUERIDO') {
        check++;
        document.getElementById("fechai").value = 'REQUERIDO';
    }

    var fechaf = document.getElementById("fechaf").value;
    if (fechaf.length == 0 || fechaf == 'REQUERIDO') {
        check++;
        document.getElementById("fechaf").value = 'REQUERIDO';
    }

    fechai = fechai + " 00:00";
    fechaf = fechaf + " 00:00";
    console.log("var " + idservicio + " " + fechai + " " + fechaf);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;

        var params = {
            "busquedaHistorial": idservicio,
            "fechai": fechai,
            "fechaf": fechaf
        };
        $.ajax({
            data: params,
            url: '../controller/route/servicios/buscarEmpleadoServicio.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablan").html(response);

            }
        });
    }

});
/*************************************************/
$(document).on('click', '#crearServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("opcion -> crear servicio ");


    var check = 0;
    var formData = new FormData();
    var cb1 = document.getElementById("cb1").value;
    if (cb1.length == 0 || cb1 == 'REQUERIDO') {
        check++;
        document.getElementById("cb1").value = 'REQUERIDO';
    }

    var cb2 = document.getElementById("cb2").value;
    if (cb2.length == 0 || cb2 == 'REQUERIDO') {
        check++;
        document.getElementById("cb2").value = 'REQUERIDO';
    }

    var cb3 = document.getElementById("cb3").value;
    if (cb3.length == 0 || cb3 == 'REQUERIDO') {
        check++;
        document.getElementById("cb3").value = 'REQUERIDO';
    }

    var municipio = document.getElementById("municipio").value;
    if (municipio.length == 0 || municipio == 'REQUERIDO') {
        check++;
        document.getElementById("municipio").value = 'REQUERIDO';
    }

    var municipio2 = document.getElementById("municipio2").value;
    if (municipio2.length == 0 || municipio2 == 'REQUERIDO') {
        check++;
        document.getElementById("municipio2").value = 'REQUERIDO';
    }

    var manifiesto = document.getElementById("manifiesto").value;
    if (manifiesto.length == 0 || manifiesto == 'REQUERIDO') {
        check++;
        document.getElementById("manifiesto").value = 'REQUERIDO';
    }

    var direccion = document.getElementById("direccion").value;
    if (direccion.length == 0 || direccion == 'REQUERIDO') {
        check++;
        document.getElementById("direccion").value = 'REQUERIDO';
    }

    var ncontenedor = document.getElementById("ncontenedor").value;
    if (ncontenedor.length == 0 || ncontenedor == 'REQUERIDO') {
        check++;
        document.getElementById("ncontenedor").value = 'REQUERIDO';
    }

    var ruta = document.getElementById("ruta").value;
    if (ruta.length == 0) { ruta = ""; }

    var sello = document.getElementById("sello").value;
    if (sello.length == 0) { sello = ""; }

    var satelital = document.getElementById("satelital").value;
    if (satelital.length == 0) { satelital = ""; }

    var link = document.getElementById("link").value;
    if (link.length == 0) { link = ""; }

    var usuario = document.getElementById("usuario").value;
    if (usuario.length == 0) { usuario = ""; }

    var clave = document.getElementById("clave").value;
    if (clave.length == 0) { clave = ""; }

    var fecha = document.getElementById("fecha").value;
    if (fecha.length == 0 || fecha == '0000-00-00') {
        check++;
        document.getElementById("fecha").value = '0000-00-00';
    }


    var file_input1 = document.getElementById("file-input1").value;
    var file_input2 = document.getElementById("file-input2").value;

    console.log("var :  " +
        cb1 + " " +
        cb2 + " " +
        cb3 + " " +
        municipio2 + " " +
        municipio + " " +
        manifiesto + " " +
        direccion + " " +
        ruta + " " +
        sello + " " +
        ncontenedor + " " +
        satelital + " " +
        link + " " +
        usuario + " " +
        clave + " " +
        file_input1 + " " +
        file_input2 + " " +
        fecha);

    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        formData.append('file-input1', $('#file-input1')[0].files[0]);
        formData.append('file-input2', $('#file-input2')[0].files[0]);
        formData.append('cb1', cb1);
        formData.append('cb2', cb2);
        formData.append('cb3', cb3);
        formData.append('municipio2', municipio2);
        formData.append('municipio', municipio);
        formData.append('manifiesto', manifiesto);
        formData.append('direccion', direccion);
        formData.append('ruta', ruta);
        formData.append('sello', sello);
        formData.append('ncontenedor', ncontenedor);
        formData.append('satelital', satelital);
        formData.append('link', link);
        formData.append('usuario', usuario);
        formData.append('clave', clave);
        formData.append('fecha', fecha);
        console.log(formData);

        $.ajax({
            url: '../controller/route/servicios/crearServicio.php',
            type: 'post',
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {},
            success: function(response) {
                if (response.indexOf("alert-warning") !== -1 || response.indexOf("alert-danger") !== -1) {
                    $("#smg_panel").html(response);
                    $("#smg_servicio").html(response);
                    return;
                }

                var recargarDespuesDeServicio = function() {
                    $.ajax({
                        url: 'administrador.php',
                        type: 'post',
                        beforeSend: function() {},
                        success: function(response) { $("#contenido").html(response); }
                    });
                };

                if (typeof swal === "function") {
                    swal({
                        title: "Servicio creado",
                        text: "El servicio se registro correctamente. Presiona aceptar para volver al panel.",
                        type: "success",
                        confirmButtonText: "Aceptar",
                        closeOnConfirm: true
                    }, function() {
                        recargarDespuesDeServicio();
                    });
                    return;
                }

                recargarDespuesDeServicio();
            }
        });
        check = 0;
    }
    // CrearPrimeraMinuta();
});
/*************************************************/
$(document).on('click', '#crearMinuta', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log("crear minuta ");
    var formData = new FormData();
    var check = 0;
    var id_empleado = document.getElementById("idempleadoHidden").value;
    var id_servicio = document.getElementById("idservicioHidden").value;


    var fecha_novedad = document.getElementById("fecha_novedad").value;
    if (fecha_novedad.length == 0 || fecha_novedad == '0000-00-00') {
        check++;
        document.getElementById("fecha_novedad").value = '0000-00-00';
    }

    var evento = document.getElementById("evento").value;
    if (evento.length == 0 || evento == 'REQUERIDO') {
        check++;
        document.getElementById("evento").value = 'REQUERIDO';
    }

    var observacion = document.getElementById("observacion").value;
    if (observacion.length == 0 || observacion == 'REQUERIDO') {
        check++;
        document.getElementById("observacion").value = 'REQUERIDO';
    }

    var nota = document.getElementById("nota").value;
    if (nota.length == 0) { nota = ""; }

    var inputImagenMinuta = $('#file-input1')[0];
    if (inputImagenMinuta && inputImagenMinuta.files && inputImagenMinuta.files.length > 0) {
        formData.append('file-input1', inputImagenMinuta.files[0]);
    }
    formData.append('idservicio', id_servicio);
    formData.append('idempleado', id_empleado);
    formData.append('fecha_novedad', fecha_novedad);
    formData.append('evento', evento);
    formData.append('observacion', observacion);
    formData.append('nota', nota);

    console.log('var ' +
        id_servicio + " " +
        id_empleado + " " +
        fecha_novedad + " " +
        evento + " " +
        observacion + " " +
        nota);

    if (parseInt(check) > 0) {
        alert('No se puede registrar la minuta puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        $.ajax({
            url: '../controller/route/servicios/crearMinuta.php',
            type: 'post',
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
           // beforeSend: function() { $("#smg_panel").html("PROCESANDO Minuta espere por favor"); },
            success: function(response) {
               //$("#smg_servicio").html(response);
                if (response.indexOf("alert-warning") !== -1 || response.indexOf("alert-danger") !== -1) {
                    /*
                    var mensajeMinuta = $("<div>").html(response).text().replace(/\s+/g, " ").trim();
                    var esWarning = response.indexOf("alert-warning") !== -1;
                    if (typeof swal === "function") {
                        swal({
                            title: esWarning ? "Minuta creada con advertencia" : "No fue posible crear la minuta",
                            text: mensajeMinuta || "No fue posible completar el proceso.",
                            type: esWarning ? "warning" : "error",
                            confirmButtonText: "Aceptar"
                        });
                    }
                    */
                    $("#smg_panel").html(response);
                    $("#smg_servicio").html(response);
                    return;
                }

                var recargarDespuesDeMinuta = function() {
                    if ($("#modalMinutaRapida").is(":visible")) {
                        $("#modalMinutaRapida").modal("hide");
                    }

                    $.ajax({
                        url: 'administrador.php',
                        type: 'post',
                        beforeSend: function() {},
                        success: function(response) { $("#contenido").html(response); }
                    });
                };

                /*
                if (typeof swal === "function") {
                    swal({
                        title: "Minuta creada",
                        text: "La minuta se registro correctamente. Presiona aceptar para volver al panel.",
                        type: "success",
                        confirmButtonText: "Aceptar",
                        closeOnConfirm: true
                    }, function() {
                        recargarDespuesDeMinuta();
                    });
                    return;
                }
                */

                recargarDespuesDeMinuta();
            }
        });
        check = 0;
    }
});

function asegurarClienteHoverCard() {
    if ($("#clienteHoverCard").length) {
        return $("#clienteHoverCard");
    }

    $("body").append(
        "<div id='clienteHoverCard' class='cliente-hover-card'>" +
        "<div class='cliente-hover-card__title'>Informacion del cliente</div>" +
        "<div class='cliente-hover-card__row'><span>Razon social</span><strong data-field='razon'></strong></div>" +
        "<div class='cliente-hover-card__row'><span>Nombre</span><strong data-field='nombre'></strong></div>" +
        "<div class='cliente-hover-card__row'><span>Ciudad</span><strong data-field='ciudad'></strong></div>" +
        "<div class='cliente-hover-card__row'><span>Telefono</span><strong data-field='telefono'></strong></div>" +
        "</div>"
    );

    return $("#clienteHoverCard");
}

function posicionarClienteHoverCard(event, $card) {
    var offset = 18;
    var left = event.pageX + offset;
    var top = event.pageY + offset;
    var ancho = $card.outerWidth();
    var alto = $card.outerHeight();
    var limiteDerecho = $(window).scrollLeft() + $(window).width() - 16;
    var limiteInferior = $(window).scrollTop() + $(window).height() - 16;

    if (left + ancho > limiteDerecho) {
        left = event.pageX - ancho - offset;
    }

    if (top + alto > limiteInferior) {
        top = event.pageY - alto - offset;
    }

    $card.css({ left: left + "px", top: top + "px" });
}

var clienteHoverTimer = null;

function ocultarClienteHoverCard() {
    clearTimeout(clienteHoverTimer);
    $("#clienteHoverCard").removeClass("is-visible");
}

$(document).on('mouseenter', '.js-cliente-hover-row', function(e) {
    var $fila = $(this);
    var $card = asegurarClienteHoverCard();

    $card.find("[data-field='razon']").text($fila.data("cliente-razon") || "No registrado");
    $card.find("[data-field='nombre']").text($fila.data("cliente-nombre") || "No registrado");
    $card.find("[data-field='ciudad']").text($fila.data("cliente-ciudad") || "No registrado");
    $card.find("[data-field='telefono']").text($fila.data("cliente-telefono") || "No registrado");
    posicionarClienteHoverCard(e, $card);
    clearTimeout(clienteHoverTimer);
    clienteHoverTimer = setTimeout(function() {
        $card.addClass("is-visible");
    }, 2000);
});

$(document).on('mousemove', '.js-cliente-hover-row', function(e) {
    posicionarClienteHoverCard(e, asegurarClienteHoverCard());
});

$(document).on('mouseleave', '.js-cliente-hover-row', function() {
    ocultarClienteHoverCard();
});

$(document).on('click scroll', function(e) {
    if (!$(e.target).closest('.js-cliente-hover-row, #clienteHoverCard').length) {
        ocultarClienteHoverCard();
    }
});

$(document).ajaxStart(function() {
    ocultarClienteHoverCard();
});

function RetornoMinuta(idServicio, responseRe) {
    var id = idServicio;
    var params = { "id": id };
    $.ajax({
        data: params,
        url: 'layout/servicios/bitacora.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) {
            $("#contenido").html(response);
            $("#smg_panel").html(responseRe);
        }
    });
};
/*************************************************/
function abrirServicioPorId(idservicio) {
    if (!idservicio) {
        swal({
            title: "No se pudo abrir",
            text: "No se encontro el servicio seleccionado.",
            type: "warning",
            confirmButtonText: "Entendido",
            customClass: "segcontrol-alert"
        });
        return;
    }

    console.log("ver servicio con id " + idservicio);
    var params = { "idservicio": idservicio };
    $.ajax({
        data: params,
        url: '../controller/route/servicios/verServicio.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
}

$(document).on('click', '#verServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();

    abrirServicioPorId($(this).attr('title'));
});
/*************************************************/
function confirmarCerrarServicio(idservicio) {
    if (!idservicio) {
        swal({
            title: "No se pudo cerrar",
            text: "No se encontro el servicio seleccionado.",
            type: "warning",
            confirmButtonText: "Entendido",
            customClass: "segcontrol-alert"
        });
        return;
    }

    swal({
        title: "Cerrar servicio",
        text: "Esta seguro de cerrar este servicio? Esta accion cambiara su estado y lo quitara de los servicios activos.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#fb483a",
        confirmButtonText: "Si, cerrar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        customClass: "segcontrol-alert"
    }, function(confirmado) {
        if (!confirmado) {
            return;
        }

        $("#smg_panel").html("");
        $("#smg_bitacora").html("");
        console.log("cerrar servicio con id " + idservicio);
        var params = { "idservicio": idservicio };
        $.ajax({
            data: params,
            url: '../controller/route/servicios/cerrarServicio.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                swal.close();
                $("#smg_panel").html(response);
                $.ajax({
                    url: 'administrador.php',
                    type: 'post',
                    beforeSend: function() {},
                    success: function(response) { $("#contenido").html(response); }
                });
            },
            error: function() {
                swal({
                    title: "No se pudo cerrar",
                    text: "Ocurrio un problema al intentar cerrar el servicio. Intenta nuevamente.",
                    type: "error",
                    confirmButtonText: "Entendido",
                    customClass: "segcontrol-alert"
                });
            }
        });
    });
}

$(document).on('click', '#cerrarServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();
    var idservicio = document.getElementById("idservicioHidden").value;
    confirmarCerrarServicio(idservicio);
});

function asegurarModalPdfServicio() {
    if ($("#modalPdfServicio").length > 0) {
        return;
    }

    $("body").append(
        "<div class='modal fade service-pdf-modal' id='modalPdfServicio' tabindex='-1' role='dialog' aria-hidden='true'>" +
            "<div class='modal-dialog' role='document'>" +
                "<div class='modal-content'>" +
                    "<div class='modal-header'>" +
                        "<h4 class='modal-title'>PDF del servicio</h4>" +
                        "<button type='button' class='close' data-dismiss='modal' aria-label='Cerrar'>" +
                            "<span aria-hidden='true'>&times;</span>" +
                        "</button>" +
                    "</div>" +
                    "<div class='modal-body' id='contenidoPdfServicio'>" +
                        "<div class='service-pdf-empty'>Cargando PDF...</div>" +
                    "</div>" +
                "</div>" +
            "</div>" +
        "</div>"
    );
}

$(document).on('click', '#verPdfServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var idservicio = document.getElementById("idservicioHidden").value;
    var pdfUrl = "../controller/route/servicios/verPdfServicio.php?idservicio=" + encodeURIComponent(idservicio) + "&v=" + Date.now();

    $.ajax({
        url: pdfUrl,
        type: "HEAD",
        success: function() {
            asegurarModalPdfServicio();
            $("#contenidoPdfServicio").html(
                "<iframe class='service-pdf-frame' src='" + pdfUrl + "' title='PDF del servicio'></iframe>"
            );
            $("#modalPdfServicio").modal("show");
        },
        error: function() {
            swal({
                title: "PDF no disponible",
                text: "Este servicio aun no tiene un PDF generado para previsualizar.",
                type: "warning",
                confirmButtonText: "Entendido",
                customClass: "segcontrol-alert"
            });
        }
    });
});

$(document).on('click', '.reenviarCorreoServicio', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var idservicio = document.getElementById('idservicioHidden').value;
    var $botones = $('.reenviarCorreoServicio');

    if (!window.confirm('Se enviara nuevamente el PDF de la ultima minuta del servicio. Deseas continuar?')) {
        return;
    }

    $botones.prop('disabled', true);
    $botones.find('.material-icons').text('hourglass_empty');

    $.ajax({
        url: '../controller/route/servicios/reenviarCorreoMinuta.php',
        type: 'post',
        dataType: 'json',
        data: { idservicio: idservicio },
        success: function(response) {
            var tipo = response.ok ? 'success' : 'warning';
            var titulo = response.ok ? 'Correo enviado' : 'Correo no enviado';

            if (typeof swal === 'function') {
                swal({
                    title: titulo,
                    text: response.mensaje,
                    type: tipo,
                    confirmButtonText: 'Aceptar'
                }, function() {
                    abrirBitacoraServicio(idservicio);
                });
            } else {
                alert(response.mensaje);
                abrirBitacoraServicio(idservicio);
            }
        },
        error: function(xhr) {
            var mensaje = 'No fue posible completar el reenvio del correo.';
            if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                mensaje = xhr.responseJSON.mensaje;
            }
            alert(mensaje);
        },
        complete: function() {
            $botones.prop('disabled', false);
            $botones.find('.material-icons').text('email');
        }
    });
});
/*************************************************/
$(document).on('click', '#verMinuta', function(e) {
    e.stopPropagation();
    e.preventDefault();
    console.log('ver minuta ');
    var idservicio = $(this).attr('title');
    console.log("ver minuta con id " + idservicio);
    var params = { "idMinuta": idservicio };
    $.ajax({
        data: params,
        url: 'layout/servicios/verMinuta.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});


/****************************************************************************/
/*      AJAX modulo de Aseguradora                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
$(document).on('click', '#aseguradoraMain', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu Aseguradora");
    $.ajax({
        url: 'layout/aseguradora/aseguradoraMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*********************************************************************************/
$(document).on('click', '#nuevoAseguradora', function(e) {
    console.log("Opcion -> nuevo Aseguradora");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> nuevo Aseguradora");
    $.ajax({
        url: 'layout/aseguradora/nuevoAseguradora.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/******************************************************************************/

/**********************************************************************************/
$(document).on('click', '#idAseguradora', function(e) {
    console.log("opcion -> ver aseguradora ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var idAseguradora = $(this).attr('title');
    console.log("var " + idAseguradora);
    var params = { "id": idAseguradora };
    $.ajax({
        data: params,
        url: 'layout/aseguradora/verAseguradora.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/******************************************************************************/
$(document).on('click', '#crearAseguradora', function(e) {
    e.stopPropagation();
    e.preventDefault();



    var check = 0;

    var id_cliente = document.getElementById("cb1").value;
    if (id_cliente.length == 0 || id_cliente == 'REQUERIDO') {
        check++;
        document.getElementById("cb1").value = 'REQUERIDO';
    }

    var id_acompanante = document.getElementById("cb2").value;
    if (id_acompanante.length == 0 || id_acompanante == 'REQUERIDO') {
        check++;
        document.getElementById("cb2").value = 'REQUERIDO';
    }

    var fecha = document.getElementById("fecha").value;
    if (fecha.length == 0 || fecha == '00-00-0000') {
        check++;
        document.getElementById("fecha").value = '00-00-0000';
    }

    var hora = document.getElementById("hora").value;
    if (hora.length == 0 || hora == '00:00:00') {
        check++;
        document.getElementById("hora").value = '00:00:00';
    }

    var valor = document.getElementById("valor").value;
    if (valor.length == 0 || valor == 'REQUERIDO') {
        check++;
        document.getElementById("valor").value = 'REQUERIDO';
    }

    var correo = document.getElementById("correo").value;
    if (correo.length == 0) { correo = ""; }

    var autorizacion = document.getElementById("autorizacion").value;
    if (autorizacion.length == 0 || autorizacion == 'REQUERIDO') {
        check++;
        document.getElementById("autorizacion").value = 'REQUERIDO';
    }

    var area = document.getElementById("area").value;
    if (area.length == 0 || area == 'REQUERIDO') {
        check++;
        document.getElementById("area").value = 'REQUERIDO';
    }

    var tipo_servicio = document.getElementById("tipo_servicio").value;
    if (tipo_servicio.length == 0 || tipo_servicio == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_servicio").value = 'REQUERIDO';
    }

    var descripcion = document.getElementById("descripcion").value;
    if (descripcion.length == 0 || descripcion == 'REQUERIDO') {
        check++;
        document.getElementById("descripcion").value = 'REQUERIDO';
    }

    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    var contenedor = document.getElementById("contenedor").value;
    if (contenedor.length == 0 || contenedor == 'REQUERIDO') {
        check++;
        document.getElementById("contenedor").value = 'REQUERIDO';
    }

    var tipo_vehiculo = document.getElementById("tipo_vehiculo").value;
    if (tipo_vehiculo.length == 0 || tipo_vehiculo == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_vehiculo").value = 'REQUERIDO';
    }

    var color = document.getElementById("color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("color").value = 'REQUERIDO';
    }


    var idhora_llegada = document.getElementById("idhora_llegada").value;
    if (idhora_llegada.length == 0 || idhora_llegada == '00:00') {
        check++;
        document.getElementById("idhora_llegada").value = '00:00';
    }

    var idhora_finalizacion = document.getElementById("idhora_finalizacion").value;
    if (idhora_finalizacion.length == 0 || idhora_finalizacion == '00:00') {
        check++;
        document.getElementById("idhora_finalizacion").value = '00:00';
    }

    console.log('var ' +
        id_cliente + " " +
        id_acompanante + " " +
        fecha + " " +
        hora + " " +
        valor + " " +
        correo + " " +
        autorizacion + " " +
        area + " " +
        tipo_servicio + " " +
        descripcion + " " +
        placa + " " +
        contenedor + " " +
        tipo_vehiculo + " " +
        color + " " +
        idhora_llegada + " " +
        idhora_finalizacion);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "cb1": id_cliente,
            "cb2": id_acompanante,
            "fecha": fecha,
            "hora": hora,
            "valor": valor,
            "autorizacion": autorizacion,
            "area": area,
            "tipo_servicio": tipo_servicio,
            "descripcion": descripcion,
            "placa": placa,
            "contenedor": contenedor,
            "tipo_vehiculo": tipo_vehiculo,
            "color": color,
            "idhora_llegada": idhora_llegada,
            "idhora_finalizacion": idhora_finalizacion
        };
        $.ajax({
            data: params,
            url: '../controller/route/aseguradora/crearAseguradora.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_Aseguradora").html(response);
            }
        });
        check = 0;
    }
});

/*********************************************************************************/
$(document).on('click', '#envioCorreo', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var check = 0;
    var mail = document.getElementById("email").value;
    if (mail.length == 0 || mail == 'REQUERIDO') {
        check++;
        document.getElementById("email").value = 'REQUERIDO';
    }


    var id_cliente = document.getElementById("cb1").value;
    if (id_cliente.length == 0 || id_cliente == 'REQUERIDO') {
        check++;
        document.getElementById("cb1").value = 'REQUERIDO';
    }

    var id_acompanante = document.getElementById("cb2").value;
    if (id_acompanante.length == 0 || id_acompanante == 'REQUERIDO') {
        check++;
        document.getElementById("cb2").value = 'REQUERIDO';
    }

    var fecha = document.getElementById("fecha").value;
    if (fecha.length == 0 || fecha == '00-00-0000') {
        check++;
        document.getElementById("fecha").value = '00-00-0000';
    }

    var hora = document.getElementById("hora").value;
    if (hora.length == 0 || hora == '00:00:00') {
        check++;
        document.getElementById("hora").value = '00:00:00';
    }

    var valor = document.getElementById("valor").value;
    if (valor.length == 0 || valor == 'REQUERIDO') {
        check++;
        document.getElementById("valor").value = 'REQUERIDO';
    }

    var correo = document.getElementById("correo").value;
    if (correo.length == 0) { correo = ""; }

    var autorizacion = document.getElementById("autorizacion").value;
    if (autorizacion.length == 0 || autorizacion == 'REQUERIDO') {
        check++;
        document.getElementById("autorizacion").value = 'REQUERIDO';
    }

    var area = document.getElementById("area").value;
    if (area.length == 0 || area == 'REQUERIDO') {
        check++;
        document.getElementById("area").value = 'REQUERIDO';
    }

    var tipo_servicio = document.getElementById("tipo_servicio").value;
    if (tipo_servicio.length == 0 || tipo_servicio == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_servicio").value = 'REQUERIDO';
    }

    var descripcion = document.getElementById("descripcion").value;
    if (descripcion.length == 0 || descripcion == 'REQUERIDO') {
        check++;
        document.getElementById("descripcion").value = 'REQUERIDO';
    }

    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    var contenedor = document.getElementById("contenedor").value;
    if (contenedor.length == 0 || contenedor == 'REQUERIDO') {
        check++;
        document.getElementById("contenedor").value = 'REQUERIDO';
    }

    var tipo_vehiculo = document.getElementById("tipo_vehiculo").value;
    if (tipo_vehiculo.length == 0 || tipo_vehiculo == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_vehiculo").value = 'REQUERIDO';
    }

    var color = document.getElementById("color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("color").value = 'REQUERIDO';
    }


    var idhora_llegada = document.getElementById("idhora_llegada").value;
    if (idhora_llegada.length == 0 || idhora_llegada == '00:00') {
        check++;
        document.getElementById("idhora_llegada").value = '00:00';
    }

    var idhora_finalizacion = document.getElementById("idhora_finalizacion").value;
    if (idhora_finalizacion.length == 0 || idhora_finalizacion == '00:00') {
        check++;
        document.getElementById("idhora_finalizacion").value = '00:00';
    }

    console.log('var ' +
        id_cliente + " " +
        id_acompanante + " " +
        fecha + " " +
        hora + " " +
        valor + " " +
        correo + " " +
        autorizacion + " " +
        area + " " +
        tipo_servicio + " " +
        descripcion + " " +
        placa + " " +
        contenedor + " " +
        tipo_vehiculo + " " +
        color + " " +
        idhora_llegada + " " +
        idhora_finalizacion);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "cb1": id_cliente,
            "cb2": id_acompanante,
            "fecha": fecha,
            "hora": hora,
            "valor": valor,
            "autorizacion": autorizacion,
            "area": area,
            "tipo_servicio": tipo_servicio,
            "descripcion": descripcion,
            "placa": placa,
            "contenedor": contenedor,
            "tipo_vehiculo": tipo_vehiculo,
            "color": color,
            "idhora_llegada": idhora_llegada,
            "idhora_finalizacion": idhora_finalizacion
        };
        $.ajax({
            data: params,
            url: '../controller/route/aseguradora/crearAseguradora.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_Aseguradora").html(response);
            }
        });
        check = 0;
    }
});



/******************************************************************************/
$(document).on('click', '#editarAseguradora', function(e) {
    e.stopPropagation();
    e.preventDefault();



    var check = 0;

    var id_aseguradora = document.getElementById("id_aseguradora").value;
    var id_cliente = document.getElementById("cb1").value;
    var id_acompanante = document.getElementById("cb2").value;
    var fecha = document.getElementById("fecha").value;
    if (fecha.length == 0 || fecha == '00-00-0000') {
        check++;
        document.getElementById("fecha").value = '00-00-0000';
    }

    var hora = document.getElementById("hora").value;
    if (hora.length == 0 || hora == '00:00') {
        check++;
        document.getElementById("hora").value = '00:00';
    }

    var valor = document.getElementById("valor").value;
    if (valor.length == 0 || valor == 'REQUERIDO') {
        check++;
        document.getElementById("valor").value = 'REQUERIDO';
    }

    var correo = document.getElementById("correo").value;
    if (correo.length == 0) { correo = ""; }

    var autorizacion = document.getElementById("autorizacion").value;
    if (autorizacion.length == 0 || autorizacion == 'REQUERIDO') {
        check++;
        document.getElementById("autorizacion").value = 'REQUERIDO';
    }

    var area = document.getElementById("area").value;
    if (area.length == 0 || area == 'REQUERIDO') {
        check++;
        document.getElementById("area").value = 'REQUERIDO';
    }

    var tipo_servicio = document.getElementById("tipo_servicio").value;
    if (tipo_servicio.length == 0 || tipo_servicio == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_servicio").value = 'REQUERIDO';
    }

    var descripcion = document.getElementById("descripcion").value;
    if (descripcion.length == 0 || descripcion == 'REQUERIDO') {
        check++;
        document.getElementById("descripcion").value = 'REQUERIDO';
    }

    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    var contenedor = document.getElementById("contenedor").value;
    if (contenedor.length == 0 || contenedor == 'REQUERIDO') {
        check++;
        document.getElementById("contenedor").value = 'REQUERIDO';
    }

    var tipo_vehiculo = document.getElementById("tipo_vehiculo").value;
    if (tipo_vehiculo.length == 0 || tipo_vehiculo == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_vehiculo").value = 'REQUERIDO';
    }

    var color = document.getElementById("color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("color").value = 'REQUERIDO';
    }


    var idhora_llegada = document.getElementById("idhora_llegada").value;
    if (idhora_llegada.length == 0 || idhora_llegada == '00:00:00') {
        check++;
        document.getElementById("idhora_llegada").value = '00:00:00';
    }

    var idhora_finalizacion = document.getElementById("idhora_finalizacion").value;
    if (idhora_finalizacion.length == 0 || idhora_finalizacion == '00:00:00') {
        check++;
        document.getElementById("idhora_finalizacion").value = '00:00:00';
    }

    console.log('var ' +
        id_cliente + " " +
        id_acompanante + " " +
        fecha + " " +
        hora + " " +
        valor + " " +
        correo + " " +
        autorizacion + " " +
        area + " " +
        tipo_servicio + " " +
        descripcion + " " +
        placa + " " +
        contenedor + " " +
        tipo_vehiculo + " " +
        color + " " +
        idhora_llegada + " " +
        idhora_finalizacion);
    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        var params = {
            "id_aseguradora": id_aseguradora,
            "cb1": id_cliente,
            "cb2": id_acompanante,
            "fecha": fecha,
            "hora": hora,
            "valor": valor,
            "autorizacion": autorizacion,
            "area": area,
            "tipo_servicio": tipo_servicio,
            "descripcion": descripcion,
            "placa": placa,
            "contenedor": contenedor,
            "tipo_vehiculo": tipo_vehiculo,
            "color": color,
            "idhora_llegada": idhora_llegada,
            "idhora_finalizacion": idhora_finalizacion
        };
        $.ajax({
            data: params,
            url: '../controller/route/aseguradora/editarAseguradora.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_Aseguradora").html(response);
            }
        });
        check = 0;
    }
});

/***********************************************************************/

$(document).on('click', '#envioCorreo', function(e) {
    e.stopPropagation();
    e.preventDefault();


    var check = 0;

    var id_cliente = document.getElementById("cb1").value;
    if (id_cliente.length == 0 || id_cliente == 'REQUERIDO') {
        check++;
        document.getElementById("cb1").value = 'REQUERIDO';
    }

    var id_acompanante = document.getElementById("cb2").value;
    if (id_acompanante.length == 0 || id_acompanante == 'REQUERIDO') {
        check++;
        document.getElementById("cb2").value = 'REQUERIDO';
    }

    if (fecha.length == 0 || fecha == '00/00/0000') {
        check++;
        document.getElementById("fecha").value = '00/00/0000';
    }

    var hora = document.getElementById("hora").value;
    if (hora.length == 0 || hora == '00:00:00') {
        check++;
        document.getElementById("hora").value = '00:00:00';
    }

    var correo = document.getElementById("correo").value;
    if (correo.length == 0 || correo == 'REQUERIDO') {
        check++;
        document.getElementById("correo").value = 'REQUERIDO';
    }

    var valor = document.getElementById("valor").value;
    if (valor.length == 0 || valor == 'REQUERIDO') {
        check++;
        document.getElementById("valor").value = 'REQUERIDO';
    }

    var autorizacion = document.getElementById("autorizacion").value;
    if (autorizacion.length == 0 || autorizacion == 'REQUERIDO') {
        check++;
        document.getElementById("autorizacion").value = 'REQUERIDO';
    }

    var area = document.getElementById("area").value;
    if (area.length == 0 || area == 'REQUERIDO') {
        check++;
        document.getElementById("area").value = 'REQUERIDO';
    }

    var tipo_servicio = document.getElementById("tipo_servicio").value;
    if (tipo_servicio.length == 0 || tipo_servicio == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_servicio").value = 'REQUERIDO';
    }

    var descripcion = document.getElementById("descripcion").value;
    if (descripcion.length == 0 || descripcion == 'REQUERIDO') {
        check++;
        document.getElementById("descripcion").value = 'REQUERIDO';
    }

    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    var contenedor = document.getElementById("contenedor").value;
    if (contenedor.length == 0 || contenedor == 'REQUERIDO') {
        check++;
        document.getElementById("contenedor").value = 'REQUERIDO';
    }

    var tipo_vehiculo = document.getElementById("tipo_vehiculo").value;
    if (tipo_vehiculo.length == 0 || tipo_vehiculo == 'REQUERIDO') {
        check++;
        document.getElementById("tipo_vehiculo").value = 'REQUERIDO';
    }

    var color = document.getElementById("color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("color").value = 'REQUERIDO';
    }


    var idhora_llegada = document.getElementById("idhora_llegada").value;
    if (idhora_llegada.length == 0 || idhora_llegada == '0000-00-00') {
        check++;
        document.getElementById("idhora_llegada").value = '0000-00-00';
    }

    var idhora_finalizacion = document.getElementById("idhora_finalizacion").value;
    if (idhora_finalizacion.length == 0 || idhora_finalizacion == '0000-00-00') {
        check++;
        document.getElementById("idhora_finalizacion").value = '0000-00-00';
    }

    console.log('var ' +
        id_cliente + " " +
        id_acompanante + " " +
        valor + " " +
        correo + " " +
        fecha + " " +
        hora + " " +
        autorizacion + " " +
        area + " " +
        tipo_servicio + " " +
        descripcion + " " +
        placa + " " +
        contenedor + " " +
        tipo_vehiculo + " " +
        color + " " +
        idhora_llegada + " " +
        idhora_finalizacion);

    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        var params = {
            "cb1": id_cliente,
            "cb2": id_acompanante,
            "valor": valor,
            "fecha": fecha,
            "hora": hora,
            "autorizacion": autorizacion,
            "area": area,
            "tipo_servicio": tipo_servicio,
            "descripcion": descripcion,
            "placa": placa,
            "contenedor": contenedor,
            "tipo_vehiculo": tipo_vehiculo,
            "color": color,
            "idhora_llegada": idhora_llegada,
            "idhora_finalizacion": idhora_finalizacion
        };
        $.ajax({
            data: params,
            url: '../controller/route/aseguradora/crearAseguradora.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_Aseguradora").html(response);
            }
        });
        check = 0;
    }
});
/****************************************************************************/
/*      AJAX modulo de Acompañante                                       */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
$(document).on('click', '#acompananteMain', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    console.log("Opcion -> menu Acompañante");
    $.ajax({
        url: 'layout/acompanante/acompananteMain.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/*********************************************************************************/
$(document).on('click', '#nuevoAcompanante', function(e) {
    console.log("Opcion -> nuevo Acompañante");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $.ajax({
        url: 'layout/acompanante/nuevoAcompanante.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

/******************************************************************************/
$(document).on('click', '#idAcompanante', function(e) {

    e.stopPropagation();
    e.preventDefault();
    console.log("Opcion -> ver Acompañante");
    $("#smg_panel").html("");
    $("#contenido").html("");
    var idAcompanante = $(this).attr('title');
    console.log("var " + idAcompanante);
    var params = { "id": idAcompanante };
    $.ajax({
        data: params,
        url: 'layout/acompanante/verAcompanante.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
/******************************************************************************/
$(document).on('click', '#buscarAcompanante', function(e) {
    e.stopPropagation();
    e.preventDefault();
    check = 0;
    var nombre = document.getElementById("busquedaAcompanante").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("busquedaAcompanante").value = 'REQUERIDO';
    }

    console.log("var " + nombre);

    if (parseInt(check) > 0) {
        alert('No se puede buscar  puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {
        check = 0;
        var params = { "nombre": nombre };
        $.ajax({
            data: params,
            url: '../controller/route/acompanante/buscarAcompanante.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#tablaacompanante").html(response);
            }
        });
    }
});




/********************************************************************************/
$(document).on('click', '#crearAcompanante', function(e) {
    e.stopPropagation();
    e.preventDefault();


    var check = 0;

    var nombre = document.getElementById("nombre").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("nombre").value = 'REQUERIDO';
    }

    var cedula = document.getElementById("cedula").value;
    if (cedula.length == 0 || cedula == 'REQUERIDO') {
        check++;
        document.getElementById("cedula").value = 'REQUERIDO';
    }

    var telefono = document.getElementById("telefono").value;
    if (telefono.length == 0 || telefono == 'REQUERIDO') {
        check++;
        document.getElementById("telefono").value = 'REQUERIDO';
    }

    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    var tipo = document.getElementById("tipo").value;
    if (tipo.length == 0 || tipo == 'REQUERIDO') {
        check++;
        document.getElementById("tipo").value = 'REQUERIDO';
    }

    var marca = document.getElementById("marca").value;
    if (marca.length == 0 || marca == 'REQUERIDO') {
        check++;
        document.getElementById("marca").value = 'REQUERIDO';
    }

    var color = document.getElementById("color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("color").value = 'REQUERIDO';
    }

    console.log('var ' +
        nombre + " " +
        cedula + " " +
        telefono + " " +
        placa + " " +
        tipo + " " +
        marca + " " +
        color);

    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        var params = {
            "nombre": nombre,
            "cedula": cedula,
            "telefono": telefono,
            "placa": placa,
            "tipo": tipo,
            "marca": marca,
            "color": color
        };
        $.ajax({
            data: params,
            url: '../controller/route/acompanante/crearAcompanante.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_Acompanante").html(response);
            }
        });

        check = 0;
    }
});

/*****************************************************************************/
$(document).on('click', '#editarAcompanante', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var check = 0;

    var id_acompanante = document.getElementById("id_acompanante").value;
    var nombre = document.getElementById("nombre").value;
    if (nombre.length == 0 || nombre == 'REQUERIDO') {
        check++;
        document.getElementById("nombre").value = 'REQUERIDO';
    }

    var cedula = document.getElementById("cedula").value;
    if (cedula.length == 0 || cedula == 'REQUERIDO') {
        check++;
        document.getElementById("cedula").value = 'REQUERIDO';
    }

    var telefono = document.getElementById("telefono").value;
    if (telefono.length == 0 || telefono == 'REQUERIDO') {
        check++;
        document.getElementById("telefono").value = 'REQUERIDO';
    }

    var placa = document.getElementById("placa").value;
    if (placa.length == 0 || placa == 'REQUERIDO') {
        check++;
        document.getElementById("placa").value = 'REQUERIDO';
    }

    var tipo = document.getElementById("tipo").value;
    if (tipo.length == 0 || tipo == 'REQUERIDO') {
        check++;
        document.getElementById("tipo").value = 'REQUERIDO';
    }

    var marca = document.getElementById("marca").value;
    if (marca.length == 0 || marca == 'REQUERIDO') {
        check++;
        document.getElementById("marca").value = 'REQUERIDO';
    }

    var color = document.getElementById("color").value;
    if (color.length == 0 || color == 'REQUERIDO') {
        check++;
        document.getElementById("color").value = 'REQUERIDO';
    }

    if (parseInt(check) > 0) {
        alert('No se puede Modificar el empleado puesto que faltan ' + check + ' campos requeridos.');
        check = 0;
    } else {

        console.log('var ' +
            id_acompanante + " " +
            nombre + " " +
            cedula + " " +
            telefono + " " +
            placa + " " +
            tipo + " " +
            marca + " " +
            color);
        var params = {
            "id_acompanante": id_acompanante,
            "nombre": nombre,
            "cedula": cedula,
            "telefono": telefono,
            "placa": placa,
            "tipo": tipo,
            "marca": marca,
            "color": color
        };
        $.ajax({
            data: params,
            url: '../controller/route/acompanante/editarAcompanante.php',
            type: 'post',
            beforeSend: function() {},
            success: function(response) {
                $("#smg_panel").html(response);
                $("#smg_Acompanante").html(response);
            }
        });
        check = 0;
    }
});


/****************************************************************************/
/*      AJAX modulo de Actividades                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
$(document).on('click', '#tiempoServicio', function(e) {
    console.log("servicios estado ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var estado = 3;
    console.log("var estado " + estado);
    var params = { "id": estado };
    $.ajax({
        data: params,
        url: 'atiempo.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

$(document).on('click', '#vencidosServicio', function(e) {
    console.log("servicios estado ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var estado = 1;
    console.log("var estado " + estado);
    var params = { "id": estado };
    $.ajax({
        data: params,
        url: 'vencimiento.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

$(document).on('click', '#prontoServicio', function(e) {
    console.log("servicios estado ");
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    var estado = 2;
    console.log("var estado " + estado);
    var params = { "id": estado };
    $.ajax({
        data: params,
        url: 'pronto.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});

/**************************************************************************/
$(document).on('click', '#trabajoMain', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $("#smg_panel").html("");
    $("#contenido").html("");
    console.log("Opcion -> trabajo colaborativo interfaz");
    $.ajax({
        url: 'layout/colaboradores/revisionTrabajoEmpleados.php',
        type: 'post',
        beforeSend: function() {},
        success: function(response) { $("#contenido").html(response); }
    });
});
