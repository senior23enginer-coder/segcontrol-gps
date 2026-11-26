<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
$controller_conductor = new conductorController();
$model_conductor = new conductor(
"NULL",
$_POST['ccConductor'],
$_POST['nameConductor'],
$_POST['telefono1'],
$_POST['telefono2'],
$_POST['telefono3']

);
if($controller_conductor->registrarConductor($model_conductor)>0){
  echo '<div class="alert alert-success">
           <strong>Registro de Conductor Completo.
        </div>';
}else{

  echo '<div class="alert alert-danger">
           <strong>error no se pudo Registrar el nuevo conductor.
        </div>';
}
?>
