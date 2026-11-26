<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
$con = new serviciosController();
$id = $_POST['idservicio'];
if($con->cerrarServicioActual(intval($id)) > 0){
  echo '<div class="alert alert-success">
           <strong>Servicio cerrado correctamente.</strong>
        </div>';
}else {
    echo '<div class="alert alert-danger">
     <strong>No se puede cerrar el servicio actual.</strong>
    </div>';
}

?>
