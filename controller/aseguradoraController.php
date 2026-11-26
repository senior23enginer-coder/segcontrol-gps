<?php 
header("access-control-allow-origin: *"); 
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/aseguradoraDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/aseguradora.php");
class aseguradoraController{

    public function registrarAseguradora(aseguradora $aseguradora){
      $ObjAseguradora=new aseguradoraDao();
      return   $ObjAseguradora->registrarAseguradora($aseguradora);
    }

    public function actualizarAseguradora(aseguradora $aseguradora){
      $ObjAseguradora=new aseguradoraDao();
      return   $ObjAseguradora->actualizarAseguradora($aseguradora);
    }

    public function listaAseguradora(){
      $ObjAseguradora=new aseguradoraDao();
      return   $ObjAseguradora->listaAseguradora();
    }

    public function aseguradoraId($id_aseguradora){
      $ObjAseguradora=new aseguradoraDao();
      return $ObjAseguradora->aseguradoraId($id_aseguradora);
    }


}


?>
