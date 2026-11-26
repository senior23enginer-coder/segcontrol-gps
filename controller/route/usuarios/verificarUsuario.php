<?php header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/controller/usuariosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/usuariosDao.php");

$controller_usuario = new usuariosController();
if($controller_usuario->verificarUsuario($_POST['user'])==1){
  echo "<span>No se puede utilizar el usuario , ya<br/> se encuentra en uso</span>";
}else{echo "<span> usuario disponible </span>";}

 ?>
