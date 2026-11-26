<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");

$controller_vehiculo = new vehiculoController();
$model_vehiculo = new vehiculo(
  $_POST['id_vehiculo'],
  $_POST['id_cliente'],
  $_POST['Placa'],
  $_POST['Marca'],
  $_POST['Color'],
  $_POST['trailer']);

if($controller_vehiculo->actualizarVehiculo($model_vehiculo)>0){
  echo '<div class="alert alert-success">
           <strong>vehiculo Actualizado .</strong>
      </div>';
}else{
  echo '<div class="alert alert-danger">
           <strong> No se pudo realizar la actualizacion del vehiculo.</strong>
           </div>';
}

?>
