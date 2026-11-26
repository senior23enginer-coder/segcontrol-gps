<?php header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/controller/usuarioController.php");
$u = $_POST['idUsuario'];
$var = new usuariosController();
$var->desactivar_Usuario($u);
?>
