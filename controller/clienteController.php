<?php 


require_once("/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/serviciosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/clienteDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/empleado.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/cliente.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");
class clienteController{

    public function registrarCliente(cliente $cliente){
      $ObjCliente=new clienteDao();
      return  $ObjCliente->registrarCliente($cliente);
    }

    public function actualizarCliente(Cliente $cliente){
      $ObjCliente=new clienteDao();
      return   $ObjCliente->actualizarCliente($cliente);
    }

    public function listaClientes(){
      $ObjCliente=new clienteDao();
      return   $ObjCliente->listaCliente();
    }

    public function listaClientesActivos(){
      $ObjCliente=new clienteDao();
      return   $ObjCliente->listaClienteActivos();
    }

    public function clienteId($id_cliente){
      $ObjCliente=new clienteDao();
      return $ObjCliente->clienteId($id_cliente);
    }

    public function clienteIdNombre($RazonSocial){
      $ObjCliente=new clienteDao();
      return $ObjCliente->clienteIdNombre($RazonSocial);
    }

    public function clienteNombre($RazonSocial){
      $ObjCliente=new clienteDao();
      return $ObjCliente->clienteNombre($RazonSocial);
    }

    public function clienteIdUsuario($id_usuario){
      $ObjCliente=new clienteDao();
      return   $ObjCliente->clienteIdUsuario($id_usuario);
    }

}


?>
