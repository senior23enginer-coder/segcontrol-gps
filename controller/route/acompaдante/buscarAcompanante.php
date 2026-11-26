<?php 
   header("access-control-allow-origin: *"); 
include_once ('https://www.segcontrol.com.co/app/segcontrol/directorio.php');
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/empleadoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/acompananteDao.php");
$con = new acompananteDao();
$nombre = $_POST['nombre'];
$arrayObj = array();
$arrayObj=$con->buscarAcompanante($nombre);
if(is_null( $arrayObj) == true || empty($arrayObj) ==true ){
  echo  "<span> No se encuentra el Acompanante! </span>";
}else{
  echo "<table class='table table-striped table-borderless'>
    <thead>
      <tr>
        <th>Nomber</th>
        <th>Cedula</th>
        <th>Placa</th>
        <th>Marca</th>
        <th>Color</th>
        <th>Ver</th>
        <th style='width:5%;' class='actions'></th>
      </tr>
     </thead>
    <tbody class='no-border-x'>";
          foreach ($arrayObj as $clave => $valor) {
            echo "<tr>
                      <td >".$arrayObj[$clave]->getNombre()."</td>
                      <td >".$arrayObj[$clave]->getCedula()."</td>
                      <td >".$arrayObj[$clave]->getPlaca()."</td>
                      <td >".$arrayObj[$clave]->getMarca()."</td>
                      <td >".$arrayObj[$clave]->getColor()."</td>
                      <td style=' class='actions'><a href='#' title='".$arrayObj[$clave]->getId_acompanante()."' id='idAcompanante' class='icon'><i class='zmdi zmdi-comment-text-alt'></i></a></td>
                  </tr>";}
    echo " </tbody>
    </table>";
}
 ?>
