<?php header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/controller/usuarioController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/usuario.php");

$controller_usuario = new usuarioController();
$model_usuario = new usuario(
$_POST['idRol'],
$_POST['usuario'],
$_POST['clave']

);
$controller_usuario->guardarUsuario($model_usuario);

?>
