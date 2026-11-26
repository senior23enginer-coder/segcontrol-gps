<?php
header("access-control-allow-origin: *");
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once('/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php');

class sesionController{

  public function validarSesion($u,$c){
    $s = new usuariosDAO();
    $obj = $s->validadSesion($u,$c);

    if($obj === false){
      echo json_encode(array(
        "estado" => "error",
        "titulo" => "Base de datos no disponible",
        "mensaje" => "No fue posible conectar con la base de datos. Verifica que el servicio MySQL este activo e intenta nuevamente."
      ));
      return;
    }

    if(is_null($obj)){
      echo json_encode(array(
        "estado" => "error",
        "titulo" => "No pudimos iniciar sesion",
        "mensaje" => "El usuario o la contrasena ingresada no coincide con nuestros registros. Verifica los datos e intenta nuevamente."
      ));
      return;
    }

    session_regenerate_id(true);
    $_SESSION['segcontrol_autenticado'] = true;
    $_SESSION['segcontrol_usuario_id'] = $obj->getId_usuario();
    $_SESSION['segcontrol_rol_id'] = $obj->getId_rol();
    $_SESSION['segcontrol_usuario'] = $obj->getUsuario();

    echo json_encode(array(
      "estado" => "ok",
      "url" => "master.php"
    ));
  }
}
?>
