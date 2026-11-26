<?php 


require_once("/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/serviciosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/conductorDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/empleado.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/cliente.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");

class conductorController{

  public function registrarConductor(conductor $conductor){
    $Objconductor=new conductorDao();
    return $Objconductor->registrarConductor($conductor);
  }

  public function actualizarConductor(conductor $conductor){
    $Objconductor=new conductorDao();
    return $Objconductor->actualizarConductor($conductor);
  }

  public function listaConductor(){
    $Objconductor=new conductorDao();
    return $Objconductor->listaConductor();
  }

  public function ConductorId($id_conductor){
    $Objconductor=new conductorDao();
    return $Objconductor->listaConductorId($id_conductor);
  }

  public function buscarConductor($Nombre){
    $Objconductor=new conductorDao();
    return $Objconductor->buscarConductor($Nombre);
  }
}
?>
