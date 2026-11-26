<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once("/home/segcontr/public_html/app/segcontrol/persistencia/consultaClienteServicioDao.php");

$idServicio = isset($_GET['idservicio']) ? intval($_GET['idservicio']) : 0;

if ($idServicio <= 0) {
  http_response_code(400);
  echo 'Servicio no valido.';
  exit;
}

$baseServicios = realpath(__DIR__ . '/../../../../servicios');
$rutaEsperada = __DIR__ . '/../../../../servicios/' . $idServicio . '/informe.pdf';
$generador = new consultaClienteServicioDao();
$generador->servicioMinuta($idServicio);
$rutaPdf = realpath($rutaEsperada);

if ($rutaPdf === false || $baseServicios === false || strpos($rutaPdf, $baseServicios) !== 0 || !is_file($rutaPdf)) {
  http_response_code(404);
  echo 'El PDF de este servicio aun no esta disponible.';
  exit;
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="servicio-' . $idServicio . '.pdf"');
header('Content-Length: ' . filesize($rutaPdf));
header('Cache-Control: private, max-age=0, must-revalidate');
readfile($rutaPdf);
