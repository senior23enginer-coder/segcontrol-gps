<?php
header("access-control-allow-origin: *");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/acompananteDao.php");

$con = new acompananteDao();
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$arrayObj = $con->buscarAcompanante($nombre);

if (is_null($arrayObj) || empty($arrayObj)) {
  echo "<span>No se encuentra el acompanante.</span>";
  exit;
}

echo "<table class='table table-striped table-borderless'>
  <thead>
    <tr>
      <th>Nombre</th>
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
    <td>".$arrayObj[$clave]->getNombre()."</td>
    <td>".$arrayObj[$clave]->getCedula()."</td>
    <td>".$arrayObj[$clave]->getPlaca()."</td>
    <td>".$arrayObj[$clave]->getMarca()."</td>
    <td>".$arrayObj[$clave]->getColor()."</td>
    <td class='actions'><a href='#' title='".$arrayObj[$clave]->getId_acompanante()."' id='idAcompanante' class='icon'><i class='zmdi zmdi-comment-text-alt'></i></a></td>
  </tr>";
}

echo "</tbody></table>";
?>
