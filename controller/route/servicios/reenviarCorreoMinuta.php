<?php
header('Content-Type: application/json; charset=UTF-8');
ob_start();

require_once('/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php');
require_once('/home/segcontr/public_html/app/segcontrol/controller/clienteController.php');
require_once('/home/segcontr/public_html/app/segcontrol/controller/minutaController.php');
require_once('/home/segcontr/public_html/app/segcontrol/controller/correoServiciosController.php');
require_once('/home/segcontr/public_html/app/segcontrol/controller/utilidades/mail.php');
require_once('/home/segcontr/public_html/app/segcontrol/persistencia/consultaClienteServicioDao.php');

date_default_timezone_set('America/Bogota');

function responderReenvio($ok, $mensaje, $estado, $fecha)
{
  if (ob_get_length()) {
    ob_clean();
  }

  echo json_encode(array(
    'ok' => $ok,
    'mensaje' => $mensaje,
    'estado' => $estado,
    'fecha_envio' => $fecha
  ));
  exit;
}

$idServicio = isset($_POST['idservicio']) ? intval($_POST['idservicio']) : 0;
if ($idServicio <= 0) {
  responderReenvio(false, 'El servicio indicado no es valido.', 'NO ENVIADO', null);
}

$controllerMinuta = new minutaController();
$idMinuta = $controllerMinuta->ultimaMinutaServicio($idServicio);
if ($idMinuta <= 0) {
  responderReenvio(false, 'El servicio no tiene minutas para reenviar.', 'NO ENVIADO', null);
}

$fechaIntento = date('Y-m-d H:i:s');
$controllerServicio = new serviciosController();
$servicio = $controllerServicio->serviciosPorId($idServicio);
if (!$servicio) {
  $controllerMinuta->actualizarEstadoEnvioCorreo($idMinuta, 'NO ENVIADO', $fechaIntento, 'No se encontro la informacion del servicio.');
  responderReenvio(false, 'No se encontro la informacion del servicio.', 'NO ENVIADO', $fechaIntento);
}

$controllerCorreo = new correoServiciosController();
$correoActual = $controllerCorreo->CorreoServiciosIdActivacion();
if (!$correoActual) {
  $controllerMinuta->actualizarEstadoEnvioCorreo($idMinuta, 'NO ENVIADO', $fechaIntento, 'No hay un correo remitente activo configurado.');
  responderReenvio(false, 'No hay un correo remitente activo configurado.', 'NO ENVIADO', $fechaIntento);
}

$generador = new consultaClienteServicioDao();
$placa = $generador->servicioMinuta($idServicio);
$controllerCliente = new clienteController();
$cliente = $controllerCliente->clienteId(intval($servicio->getId_cliente()));
if (!$cliente) {
  $controllerMinuta->actualizarEstadoEnvioCorreo($idMinuta, 'NO ENVIADO', $fechaIntento, 'No se encontro el cliente asociado al servicio.');
  responderReenvio(false, 'No se encontro el cliente asociado al servicio.', 'NO ENVIADO', $fechaIntento);
}

$mailer = new mail();
$resultado = $mailer->correo(
  $cliente,
  'operaciones.segcontrol@gmail.com',
  'segcontrolgps@gmail.com',
  'Reporte de Minuta Vehiculo (' . $placa . ')',
  'Reporte de Minuta Vehiculo (' . $placa . ')',
  $correoActual->getCorreo(),
  $correoActual->getClave(),
  $idServicio
);

$enviado = is_array($resultado) && isset($resultado['ok']) && $resultado['ok'];
$estado = $enviado ? 'ENVIADO' : 'NO ENVIADO';
if (!$enviado) {
  $detalle = is_array($resultado) && isset($resultado['detalle']) ? trim($resultado['detalle']) : '';
  $mensaje = is_array($resultado) && isset($resultado['mensaje'])
    ? $resultado['mensaje']
    : 'No fue posible reenviar el correo.';
  $diagnostico = $mensaje . ($detalle !== '' ? ' Detalle: ' . $detalle : '');
  $controllerMinuta->actualizarEstadoEnvioCorreo($idMinuta, $estado, $fechaIntento, $diagnostico);
  responderReenvio(false, $diagnostico, $estado, $fechaIntento);
}

$controllerMinuta->actualizarEstadoEnvioCorreo($idMinuta, $estado, $fechaIntento, 'Correo enviado correctamente.');
responderReenvio(true, 'Correo enviado nuevamente de forma correcta.', $estado, $fechaIntento);
?>
