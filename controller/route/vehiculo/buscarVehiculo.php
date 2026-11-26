<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/empleadoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/vehiculoDao.php");
$con = new vehiculoDao();
$nombre = $_POST['nombre'];
$arrayObj = array();
$arrayObj=$con->buscarVehiculo($nombre);
if(is_null( $arrayObj) == true || empty($arrayObj) ==true ){
  echo  "<span> No se encuentra el vehiculo! </span>";
}else{
  echo "<table class='table table-striped table-borderless'>
    <thead>
      <tr>
        <th>vehiculo</th>
        <th>Placa</th>
        <th>Marca</th>
        <th>Color</th>
        <th>Trailer</th>

        <th style='width:5%;' class='actions'></th>
      </tr>
     </thead>
    <tbody class='no-border-x'>";
          foreach ($arrayObj as $clave => $valor) {
            echo "<tr>
                      <td >".$arrayObj[$clave]->getId_vehiculo()."</td>
                      <td >".$arrayObj[$clave]->getPlaca()."</td>
                      <td >".$arrayObj[$clave]->getMarca()."</td>
                      <td >".$arrayObj[$clave]->getColor()."</td>
                      <td >".$arrayObj[$clave]->getN_trailer()."</td>
                      <td style=' class='actions'><a href='#' title='".$arrayObj[$clave]->getId_vehiculo()."' id='idVehiculo' class='icon'><i class='zmdi zmdi-comment-text-alt'></i></a></td>
                  </tr>";}
    echo " </tbody>
    </table>";
}
 ?>
