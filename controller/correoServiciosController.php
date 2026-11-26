<?php 
header("access-control-allow-origin: *"); 

    require_once("/home/segcontr/public_html/app/segcontrol/persistencia/correoServiciosDao.php");
    require_once("/home/segcontr/public_html/app/segcontrol/model/correoServicios.php");

class correoServiciosController{

  public function registrarCorreoServicios(correoServicios $correoServicios){
    $ObjcorreoServicios=new correoServiciosDao();
    return $ObjcorreoServicios->registrarCorreoServicios($correoServicios);
  }

  public function actualizarCorreoServicios($id,$act){
    $ObjcorreoServicios=new correoServiciosDao();
    return $ObjcorreoServicios->actualizarCorreoServicios($id,$act);
  }

  public function actualizarCorreoServiciosActivacion($act){
    $ObjcorreoServicios=new correoServiciosDao();
    return $ObjcorreoServicios->actualizarCorreoServiciosActivacion($act);
  }

  public function listaCorreoServicios(){
    $ObjcorreoServicios=new correoServiciosDao();
    return $ObjcorreoServicios->listaCorreoServicios();
  }

  public function correoServiciosIdActivacion(){
    $ObjcorreoServicios=new correoServiciosDao();
    return $ObjcorreoServicios->correoServiciosIdActivacion();
  }
}
?>