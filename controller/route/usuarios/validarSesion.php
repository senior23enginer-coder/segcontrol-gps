<?php 
header("access-control-allow-origin: *"); 
header("Content-Type: application/json; charset=utf-8");
require_once("../../sesionController.php");
$u = isset($_POST['usuario']) ? trim($_POST['usuario']) : "";
$c = isset($_POST['clave']) ? $_POST['clave'] : "";
$var = new sesionController();
$var->validarSesion($u,$c);
?>
