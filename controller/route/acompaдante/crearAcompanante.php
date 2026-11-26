<?php 
   header("access-control-allow-origin: *"); 
include_once ('https://www.segcontrol.com.co/app/segcontrol/directorio.php');
require_once("/home/segcontr/public_html/app/segcontrol/controller/acompananteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/acompanante.php");
$controller_acompañante = new acompananteController();
$model_acompañante = new acompanante(
"NULL",
$_POST['nombre'],
$_POST['cedula'],
$_POST['telefono'],
$_POST['placa'],
$_POST['tipo'],
$_POST['marca'],
$_POST['color']
);
if($controller_acompañante->registrarAcompanante($model_acompañante)>0){
  echo '<div class="alert alert-success">
           <strong>Registro de Acompañante Completo.
        </div>';
}else{
  echo '<div class="alert alert-danger">
           <strong>No se pudo registrar el Acompañante
        </div>';
}
?>
