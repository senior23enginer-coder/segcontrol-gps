<?php 
header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/acompananteDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/acompanante.php");


  class acompananteController {

    public function registrarAcompanante(acompanante $acompanante){
      $ObjAcompanante=new acompananteDao();
      return   $ObjAcompanante->registrarAcompanante($acompanante);
    }

    public function actualizarAcompanante(acompanante $acompanante){
      $ObjAcompanante=new acompananteDao();
      return   $ObjAcompanante->actualizarAcompanante($acompanante);
    }

    public function listaAcompanante(){
      $ObjAcompanante=new acompananteDao();
      return  $ObjAcompanante->listaAcompanante();
    }

    public function acompananteId($id_acompanante){
      $ObjAcompanante=new acompananteDao();
      return $ObjAcompanante->acompananteId($id_acompanante);
    }

    public function acompananteNombre($Nombre){
      $ObjAcompanante=new acompananteDao();
      return $ObjAcompanante->acompananteNombre($Nombre);
    }
  }

?>
