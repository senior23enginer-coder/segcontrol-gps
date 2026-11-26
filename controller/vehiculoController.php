<?php 


require_once("/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/serviciosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/vehiculoDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/empleado.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/cliente.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");

class vehiculoController{

  public function registrarVehiculo(vehiculo $vehiculo){
    $ObjVehiculo=new vehiculoDao();
    return $ObjVehiculo->registrarVehiculo($vehiculo);
  }

  public function  actualizarVehiculo(vehiculo $vehiculo){
    $ObjVehiculo=new vehiculoDao();
    return $ObjVehiculo->actualizarVehiculo($vehiculo);
  }

  public function listaVehiculos(){
    $ObjVehiculo=new vehiculoDao();
    return   $ObjVehiculo->listaVehiculo();
  }

  public function vehiculoId($id_vehiculo){
    $ObjVehiculo=new vehiculoDao();
    return $ObjVehiculo->listaVehiculoId($id_vehiculo);
  }

  public function vehiculosPorCliente($id_cliente){
    $ObjVehiculo=new vehiculoDao();
    return $ObjVehiculo->listaVehiculoCli($id_cliente);
  }

  public function buscarVehiculo($placa){
    $ObjVehiculo=new vehiculoDao();
    return $ObjVehiculo->buscarVehiculo($placa);
  }


}
?>
