<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/sesionController.php");
$u = $_POST['usuario'];
$c = $_POST['clave'];
$var = new sesionController();
$var->validarSesion($u,$c);

?>
