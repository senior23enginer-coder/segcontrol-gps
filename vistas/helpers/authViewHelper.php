<?php

if (!function_exists('sc_login_url')) {
  function sc_login_url() {
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $vistasPos = strpos($scriptName, '/vistas/');

    if ($vistasPos === false) {
      return 'index.php';
    }

    return substr($scriptName, 0, $vistasPos + strlen('/vistas/')) . 'index.php';
  }
}

if (!function_exists('sc_usuario_autenticado')) {
  function sc_usuario_autenticado() {
    return isset($_SESSION['segcontrol_autenticado'])
      && $_SESSION['segcontrol_autenticado'] === true
      && isset($_SESSION['segcontrol_usuario_id'], $_SESSION['segcontrol_rol_id']);
  }
}

if (!function_exists('sc_requerir_sesion_autenticada')) {
  function sc_requerir_sesion_autenticada() {
    if (sc_usuario_autenticado()) {
      return;
    }

    if (!headers_sent()) {
      header('Location: ' . sc_login_url());
      exit;
    }

    echo '<script>window.location.replace("' . htmlspecialchars(sc_login_url(), ENT_QUOTES, 'UTF-8') . '");</script>';
    exit;
  }
}

if (!function_exists('sc_sesion_usuario_id')) {
  function sc_sesion_usuario_id() {
    return isset($_SESSION['segcontrol_usuario_id']) ? $_SESSION['segcontrol_usuario_id'] : 0;
  }
}

if (!function_exists('sc_sesion_rol_id')) {
  function sc_sesion_rol_id() {
    return isset($_SESSION['segcontrol_rol_id']) ? $_SESSION['segcontrol_rol_id'] : 0;
  }
}

?>
