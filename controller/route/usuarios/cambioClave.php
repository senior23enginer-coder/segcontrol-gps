<?php header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/controller/usuarioController.php");
$u = $_POST['claveActual'];
$c = $_POST['claveNueva'];
$var = new usuarioController();
$var->cambioClave($u,$c);
?>
