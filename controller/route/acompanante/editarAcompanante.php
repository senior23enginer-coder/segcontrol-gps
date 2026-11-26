<?php
header("access-control-allow-origin: *");
require_once("/home/segcontr/public_html/app/segcontrol/controller/acompananteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/acompanante.php");

$controller = new acompananteController();
$model = new acompanante(
  $_POST['id_acompanante'],
  $_POST['nombre'],
  $_POST['cedula'],
  $_POST['telefono'],
  $_POST['placa'],
  $_POST['tipo'],
  $_POST['marca'],
  $_POST['color']
);

if ($controller->actualizarAcompanante($model) > 0) {
  echo '<div class="alert alert-success"><strong>Acompanante actualizado.</strong></div>';
} else {
  echo '<div class="alert alert-danger"><strong>No se pudo actualizar el acompanante.</strong></div>';
}
?>
