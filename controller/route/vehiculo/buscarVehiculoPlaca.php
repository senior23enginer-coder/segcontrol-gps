<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");

$con = new vehiculoController();
$placa=$_POST['Placa'];
$arrayObj = array();
$arrayObj=$con->buscarVehiculo($placa);
if(is_null( $arrayObj) == true || empty($arrayObj) ==true ){
  echo  "<span> No se encuentra la placa del vehiculo! </span>";
}else{
  echo "<div class='body'>
            <div class='row clearfix'>
                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                     <p> <b> Placa de vehiculos con su cliente asignado </b> </p>
                        <div class='btn-group  form-control ''>
                        <select name='cb2' id='cb2' class='form-control '>";
                          foreach ($arrayObj as $clave => $valor) {
                            echo "<option value='".$arrayObj[$clave]->getId_vehiculo()."'>vehiculo placa : ".$arrayObj[$clave]->getPlaca()."</option>";  
                        }
                    echo "</select>
                  </div>
                </div>                
            </div>
        </div>";
    }
 ?>

