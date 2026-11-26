<?php header("access-control-allow-origin: *"); 

require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");

require_once("/home/segcontr/public_html/app/segcontrol/controller/empleadoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/clienteDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/usuariosDao.php");
$con = new clienteDao();
$con2 = new usuariosDao();
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$arrayObj = array();
$arrayObj=$con->clienteNombre($nombre);
if(is_null( $arrayObj) == true || empty($arrayObj) ==true ){
  echo  "<span> No se encuentra el cliente! </span>";
}else{
  echo "
              <table class='table table-striped table-borderless'>
                   <thead>
                     <tr>
                       <th>Nombre</th>
                       <th>Razon Social</th>
                       <th>Ciudad</th>
                       <th>Usuario Asignado</th>

                       <th style='width:5%;' class='actions'></th>
                     </tr>
                    </thead>
                    <tbody class='no-border-x'>";
                         foreach ($arrayObj as $clave => $valor) {
                          $arrayObjUsu = $con2->usuarioId( intval($arrayObj[$clave]->getId_usuario()) );

                          if(intval($arrayObjUsu->getId_rol())==1){
                             $rol="administrador";
                          }else{
                              if(intval($arrayObjUsu->getId_rol())==2){
                                $rol="asistente";
                              }else{
                                $rol="cliente";
                                }
                          }

                           echo "<tr>
                                     <td >".$arrayObj[$clave]->getNombre()."</td>
                                     <td >".$arrayObj[$clave]->getRazon_social()."</td>
                                     <td >".$arrayObj[$clave]->getCiudad()."</td>
                                     <td >".$arrayObjUsu->getUsuario()."</td>
                                     <td >".$rol."</td>
                                     <td style=' class='actions'><a href='#' title='".$arrayObj[$clave]->getId_cliente()."' id='idCliente' class='icon'><i class='zmdi zmdi-comment-text-alt'></i></a></td>
                                 </tr>";
                          }
             echo " </tbody>
                </table>";
}
 ?>
