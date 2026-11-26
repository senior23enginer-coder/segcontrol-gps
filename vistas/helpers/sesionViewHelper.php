<?php

if (!function_exists('sc_crear_contexto_sesion')) {
  function sc_crear_contexto_sesion($idUsuario, $rolUsuario) {
    $rolUsuario = intval($rolUsuario);

    if ($rolUsuario === 1 || $rolUsuario === 2) {
      $controller = new EmpleadoController();
      $empleado = $controller->empleadoIdUsuario($idUsuario);
      $rol = $rolUsuario === 1 ? 'administrador' : 'asistente';

      return array(
        'id_usuario' => $idUsuario,
        'rol' => $rol,
        'foto' => is_object($empleado) ? $empleado->getFoto() : '',
        'nombre' => is_object($empleado) ? $empleado->getNombre() : '',
        'id' => is_object($empleado) ? $empleado->getId_empleado() : 0
      );
    }

    $controller = new clienteController();
    $cliente = $controller->clienteIdUsuario($idUsuario);

    return array(
      'id_usuario' => $idUsuario,
      'rol' => 'cliente',
      'foto' => '../assets/images/random-avatar4.jpg',
      'nombre' => is_object($cliente) ? $cliente->getNombre() : '',
      'id' => is_object($cliente) ? $cliente->getId_cliente() : 0
    );
  }
}

if (!function_exists('sc_aplicar_contexto_sesion')) {
  function sc_aplicar_contexto_sesion($contexto) {
    $_SESSION['id'] = $contexto['id'];
    $_SESSION['rol'] = $contexto['rol'];

    if ($contexto['rol'] === 'cliente') {
      $_SESSION['nombre'] = $contexto['nombre'];
    }
  }
}

?>
