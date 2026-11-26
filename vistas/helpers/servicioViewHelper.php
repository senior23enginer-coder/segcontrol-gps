<?php

if (!function_exists('sc_valor_objeto')) {
  function sc_valor_objeto($objeto, $metodo, $textoFallback) {
    if (is_object($objeto) && method_exists($objeto, $metodo)) {
      return $objeto->$metodo();
    }

    return $textoFallback;
  }
}

if (!function_exists('sc_conductor_nombre')) {
  function sc_conductor_nombre($conductor, $idConductor) {
    return sc_valor_objeto($conductor, 'getNombre_conductor', 'Conductor no encontrado (ID '.$idConductor.')');
  }
}

if (!function_exists('sc_vehiculo_placa')) {
  function sc_vehiculo_placa($vehiculo, $idVehiculo) {
    return sc_valor_objeto($vehiculo, 'getPlaca', 'Vehiculo no encontrado (ID '.$idVehiculo.')');
  }
}

if (!function_exists('sc_cliente_razon_social')) {
  function sc_cliente_razon_social($cliente, $idCliente) {
    return sc_valor_objeto($cliente, 'getRazon_social', 'Cliente no encontrado (ID '.$idCliente.')');
  }
}

if (!function_exists('sc_cliente_nombre')) {
  function sc_cliente_nombre($cliente, $idCliente) {
    return sc_valor_objeto($cliente, 'getNombre', 'Cliente no encontrado (ID '.$idCliente.')');
  }
}

if (!function_exists('sc_estado_minuta_texto')) {
  function sc_estado_minuta_texto($estado, $conIcono = true) {
    $estado = intval($estado);

    if ($estado === 3) {
      $icono = $conIcono ? "<i class='material-icons'>alarm_on</i>" : "";
      return "<td  class='text-success'>".$icono."A tiempo </td>";
    }

    if ($estado === 2) {
      $icono = $conIcono ? "<i class='material-icons'>assignment_late</i>" : "";
      return "<td  class='text-warning'>".$icono."Pronto a vencer</td>";
    }

    $icono = $conIcono ? "<i class='material-icons'>alarm_off</i>" : "";
    return "<td  class='text-danger'>".$icono."Vencido</td>";
  }
}

if (!function_exists('sc_clase_estado_minuta')) {
  function sc_clase_estado_minuta($estado) {
    $estado = intval($estado);

    if ($estado === 3) {
      return 'text-success';
    }

    if ($estado === 2) {
      return 'text-warning';
    }

    return 'text-danger';
  }
}

?>
