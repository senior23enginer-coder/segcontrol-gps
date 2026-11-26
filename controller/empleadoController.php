<?php 
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/serviciosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/empleadoDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/empleado.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/cliente.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/usuario.php");

class empleadoController{

  public function registrarEmpleado(empleado $empleado){
    $ObjEmpleado=new empleadoDao();
    return   $ObjEmpleado->registrarEmpleado($empleado);
  }

  public function actualizarEmpleado(empleado $empleado){
    $ObjEmpleado=new empleadoDao();
    return   $ObjEmpleado->actualizarEmpleado($empleado);
  }

  public function listaEmpleados(){
     $ObjEmpleado = new empleadoDao();
     return $ObjEmpleado->listaEmpleados();
  }

  public function empleadoId($id_empleado){
    $ObjEmpleado=new empleadoDao();
    return $ObjEmpleado->empleadoId($id_empleado);
  }

  public function empleadoIdUsuario($id_usuario){
    $ObjEmpleado=new empleadoDao();
    return   $ObjEmpleado->empleadoIdUsuario($id_usuario);
  }


}
?>
