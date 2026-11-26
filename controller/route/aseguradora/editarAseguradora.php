<?php header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/controller/aseguradoraController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/aseguradora.php");
$controller_aseguradora = new aseguradoraController();
$model_aseguradora = new aseguradora(
$_POST['id_aseguradora'],
$_POST['valor'],
$_POST['fecha'],
$_POST['hora'],
$_POST['autorizacion'],
$_POST['area'],
$_POST['tipo_servicio'],
$_POST['descripcion'],
$_POST['placa'],
$_POST['contenedor'],
$_POST['tipo_vehiculo'],
$_POST['color'],
$_POST['cb2'],
$_POST['idhora_llegada'],
$_POST['idhora_finalizacion'],
$_POST['cb1']
);
if($controller_aseguradora->actualizarAseguradora($model_aseguradora)>0){

  echo '<div class="alert alert-success">
           <strong> Aseguradora actualizada.
        </div>';
}else{

  echo '<div class="alert alert-danger">
           <strong>No se pudo actualizar la aseguradora.
        </div>';
}
?>
