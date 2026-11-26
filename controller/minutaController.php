<?php 

require_once("/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/minutaDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/serviciosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/empleado.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/cliente.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");

class minutaController{

  public function registrarMinuta(minuta $minuta){
    $Objminuta=new minutaDao();
    return $Objminuta->registrarMinuta($minuta);
  }

  public function actualizarMinuta(minuta $minuta){
    $Objminuta=new minutaDao();
    return $Objminuta->actualizarMinuta($minuta);
  }

  public function listaMinutas(){
    $Objminuta=new minutaDao();
    return $Objminuta->listaGeneralMinuta();
  }

  public function minutasPorEmpleado($id_empleado){
    $Objminuta=new minutaDao();
    return $Objminuta->listaMinutaEm($id_empleado);
  }

  public function minutasPorServicios($id_servicio){
    $Objminuta=new minutaDao();
    return $Objminuta->listaMinutaId($id_servicio);
  }

  public function minutaPorId($id_minuta){
   $Objminuta=new minutaDao();
   return $Objminuta->minutaPorId($id_minuta);
 }

  public function actualizarEstadoEnvioCorreo($id_minuta, $estado, $fecha_envio, $detalle=null){
    $Objminuta=new minutaDao();
    return $Objminuta->actualizarEstadoEnvioCorreo($id_minuta, $estado, $fecha_envio, $detalle);
  }

  public function ultimaMinutaServicio($id_servicio){
    $Objminuta=new minutaDao();
    return $Objminuta->ultimaMinutaServicio($id_servicio);
  }

  public function ultimoCorreoEnviadoServicio($id_servicio){
    $Objminuta=new minutaDao();
    return $Objminuta->ultimoCorreoEnviadoServicio($id_servicio);
  }

  public function minutaIdMAX(){

    $Objminuta=new minutaDao();
    return $Objminuta->ultimoID();
  }



}
?>
