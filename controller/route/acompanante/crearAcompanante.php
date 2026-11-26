<?php
header("access-control-allow-origin: *");
require_once("/home/segcontr/public_html/app/segcontrol/controller/acompananteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/acompanante.php");

$controller = new acompananteController();
$model = new acompanante(
  "NULL",
  $_POST['nombre'],
  $_POST['cedula'],
  $_POST['telefono'],
  $_POST['placa'],
  $_POST['tipo'],
  $_POST['marca'],
  $_POST['color']
);

if ($controller->registrarAcompanante($model) > 0) {
  echo '<div class="alert alert-success"><strong>Registro de acompanante completo.</strong></div>';
} else {
  echo '<div class="alert alert-danger"><strong>No se pudo registrar el acompanante.</strong></div>';
}
?>
