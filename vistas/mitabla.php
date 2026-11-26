<?php 
if (!ob_get_level()) {
  ob_start();
}
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once("/home/segcontr/public_html/app/segcontrol/controller/usuariosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/empleadoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once(__DIR__."/helpers/authViewHelper.php");
require_once(__DIR__."/helpers/sesionViewHelper.php");

$limpiarUrl = !empty($_SERVER['QUERY_STRING']);
sc_requerir_sesion_autenticada();
$sesion_id = sc_sesion_usuario_id();
$rol = sc_sesion_rol_id();

if ($limpiarUrl && !headers_sent()) {
  header('Location: mitabla.php');
  exit;
}

$contextoSesion = sc_crear_contexto_sesion($sesion_id, $rol);
sc_aplicar_contexto_sesion($contextoSesion);

$sesion_rol = $contextoSesion['rol'];
$sesion_foto = $contextoSesion['foto'];
$sesion_nombre = $contextoSesion['nombre'];
$id = $contextoSesion['id'];
?>
<!DOCTYPE html>
<html>
<?php require_once("head.php");?>
<body class="theme-blue">
  <input id="rolHidden" name="rolHidden" type="hidden" value="<?php echo $sesion_rol; ?>" />
  

      <div class="container-fluid">
        <div class="row clearfisx">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div id="contenido">
              <?php 
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/serviciosDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/empleadoDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/clienteDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/conductorDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/vehiculoDao.php");

 ?>

<input id='cargar' name='cargar' type='hidden' value='carga' />
<div class=" alert alert bg-teal">
  <strong>Estadisticas del sistema.<div id="refresco"></div></strong>
</div>
<div class="row clearfix">
    
      
   <?php 

   $objServi= new serviciosController();
   $objdao= new serviciosDao();
   $arrayObj = array();
   $arrayObj = $objServi->listaTodosServiciosActivos();
   if(is_null( $arrayObj) == true || empty($arrayObj) ==true ){
     echo "
     <div class='row clearfix'>
       <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
        <div class='card'>
          <div class='body' id='footer'>
            <div class='row'>
              <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                <div class'header'>
                  <h2>No hay Servicios Activos .</h2>
                </div>
                <div class='text-muted-dk'>Desea registrar un primer servicio en su sistema .</div>
                <br>
                 <button type='button' id='nuevoServicio' class='btn btn-raised btn-default waves-effect'>
                   <i class='zmdi zmdi-developer-board zmdi-hc-4x'></i>
                 </button>
                </div>
              </div>
            </div>

          </div>
      </div>
     ";
   }else{

  echo "<div id='tablax'> <div class='row clearfix '>
           <div class='col-xs-12 col-sm-12 col-md-12'>";


             echo "<div class='card aos-item' data-aos-duration='400' data-aos-delay='100' d ata-aos='fade-up'>
               <div class='header'>
                 <h2>Sistema de Notificaciones :  Minutas de servicios Activos.</h2>
               </div>
               <div class='body table-responsive'>
                 <input id='estadoHidden' name='estadoHidden' type='hidden' value='0'/>
                 <table class='table table-striped table-borderless'>
                   <thead>
                     <tr>
                       <th>ID</th>

                       <th>Vehiculo</th>
                     

                     </tr>
                    </thead>
                   <tbody class='no-border-x' >
                     ";
                     $c = 0;
                    foreach ($arrayObj as $clave => $valor) {
                      
                      $estado = $objServi->verificarMinuta($arrayObj[$clave]->getId_servicio());

                      $objCliente =new clienteController();
                      $arrayObj1=$objCliente->clienteId($arrayObj[$clave]->getId_cliente());

                      $objConductor=new conductorController();
                      $arrayObj2=$objConductor->ConductorId($arrayObj[$clave]->getId_conductor());

                      $objVehiculo=new vehiculoController();
                      $arrayObj3=$objVehiculo->vehiculoId($arrayObj[$clave]->getId_vehiculo());
                      $c++;

                      echo "<tr>
                                <td >".$c."</td>

                                <td >";
                                if(intval($estado)==3 ){
                                  echo "<a href='#' title='".$arrayObj[$clave]->getId_servicio()."' id='bitacora' class='text-success' ><p>".$arrayObj3->getPlaca()."<p>
                                </a>";
                              }else {
                                if(intval($estado)==2 ){
                                    echo "<a href='#' title='".$arrayObj[$clave]->getId_servicio()."' id='bitacora' class='text-warning' ><p>".$arrayObj3->getPlaca()."<p>
                                </a>";
                                   }else {
                                    echo "<a href='#' title='".$arrayObj[$clave]->getId_servicio()."' id='bitacora' class='text-danger' ><p>".$arrayObj3->getPlaca()."<p>
                                </a>";
                                  }
                                }
                                
                              
                               echo "
                            </tr>";
                          }
              echo "
                   </tbody>
                  </table>
                 </div>
               </div>
            </div>
          </div>
        </div></div>";
   }
     ?>
     <script>
          clearInterval(intervalo);
          var intervalo = setInterval(  function showSeconds(){
              console.log("verificador de estados de minutas ");
              var carga=document.getElementById("cargar");
              if(typeof carga == "undefined" ){
               console.log("carga fallida ");
              }else{
                if(document.getElementById("cargar").value == "carga" ){
                  console.log("carga reactiva funcionando");
                  url="mitabla.php";
                   $.ajax({
                     url:   url,
                     type:  'post',
                     beforeSend: function () {},
                     success:  function (response) {$("#contenido").html(response);}});
                }else{
                  console.log("no es la interfaz del administrador ");
                }
              }
            },60000);
     </script>


            </div>
          </div>
        </div>


              </div>

        
            <?php 
              require_once("footer.php");?>
          </body>
          </html>
