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
require_once(__DIR__."/helpers/servicioViewHelper.php");

function segcontrolPorcentajeUsoDisco()
{
  $cuotaBytes = 10000000000;
  $cachePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'segcontrol_disk_usage.json';
  $cacheTtl = 300;

  if (is_file($cachePath) && (time() - filemtime($cachePath)) < $cacheTtl) {
    $cache = json_decode((string) @file_get_contents($cachePath), true);
    if (is_array($cache) && isset($cache['bytes'])) {
      return min(100, max(0, (int) round(($cache['bytes'] / $cuotaBytes) * 100)));
    }
  }

  $homePath = '/home/segcontr';
  $bytes = 0;
  if (is_dir($homePath)) {
    try {
      $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($homePath, FilesystemIterator::SKIP_DOTS)
      );
      foreach ($iterator as $item) {
        if ($item->isFile() && !$item->isLink()) {
          $bytes += $item->getSize();
        }
      }
      @file_put_contents($cachePath, json_encode(array('bytes' => $bytes)));
    } catch (Exception $error) {
      return 0;
    }
  }

  return min(100, max(0, (int) round(($bytes / $cuotaBytes) * 100)));
}

 ?>

<input id='cargar' name='cargar' type='hidden' value='carga' />

   
   <?php 

   $objServi= new serviciosController();
   $objdao= new serviciosDao();
   $porcentajeDisco = segcontrolPorcentajeUsoDisco();
   $totalVencidos = intval($objdao->nMinutavencidos());
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

  $panelFlotanteUrl = "mitabla.php";
  echo "<div id='tablax'> <div class='row clearfix '>
           <div class='col-xs-12 col-sm-12 col-md-12'>";
     echo  "
            <button type='button' id='tiempoServicio' class='btn  btn-raised btn-success btn-lg waves-effect'><i class='material-icons'>alarm_on</i> A Tiempo (".$objdao->nMinutaTiempo().") </button>
            <button type='button' id='prontoServicio' class='btn  btn-raised btn-warning btn-lg waves-effect'><i class='material-icons'>assignment_late</i> Pronto a Vencer (".$objdao->nMinutaPronto().")</button>
            <button type='button' id='vencidosServicio' class='btn  btn-raised btn-danger btn-lg  waves-effect'> <i class='material-icons'>alarm_off</i>Vencidos (".$totalVencidos.")</button>
            <input type='hidden' id='serviciosVencidosCount' value='".$totalVencidos."' />
            <a
  href='".$panelFlotanteUrl."'
  target='popup'
  onclick=\"window.open(this.href, 'popup', 'width=350,height=600'); return false;\">
  <i class='material-icons'>assignment_late</i>&nbsp;&nbsp;Panel de servicios flotante 
</a>";
echo "<div class='alert alert-success'>
                            <strong>Version 2.11.22.26 | Disco: ".$porcentajeDisco."%</strong>
                        </div>";

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
                       <th>Conductor</th>
                       <th>Vehiculo</th>
                       <th>Cliente</th>
                       <th>Ruta</th>
                       <th>Seguimiento</th>
                       <th style='width:5%;' class='actions'></th>
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
                      $nombreConductor = sc_conductor_nombre($arrayObj2, $arrayObj[$clave]->getId_conductor());
                      $placaVehiculo = sc_vehiculo_placa($arrayObj3, $arrayObj[$clave]->getId_vehiculo());
                      $razonCliente = sc_cliente_razon_social($arrayObj1, $arrayObj[$clave]->getId_cliente());
                      $nombreCliente = sc_cliente_nombre($arrayObj1, $arrayObj[$clave]->getId_cliente());
                      $ciudadCliente = sc_valor_objeto($arrayObj1, 'getCiudad', 'Ciudad no registrada');
                      $telefonoCliente = sc_valor_objeto($arrayObj1, 'getCelular1', 'Telefono no registrado');
                      $claseEstado = sc_clase_estado_minuta($estado);
                      $c++;

                      echo "<tr class='js-servicio-activo-row js-cliente-hover-row'
                                data-id-servicio='".$arrayObj[$clave]->getId_servicio()."'
                                data-cliente-razon='".htmlspecialchars($razonCliente, ENT_QUOTES, 'UTF-8')."'
                                data-cliente-nombre='".htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8')."'
                                data-cliente-ciudad='".htmlspecialchars($ciudadCliente, ENT_QUOTES, 'UTF-8')."'
                                data-cliente-telefono='".htmlspecialchars($telefonoCliente, ENT_QUOTES, 'UTF-8')."'>
                                <td >".$c."</td>
                                <td >".$nombreConductor."</td>
                                <td >";
                                echo "<a href='#' title='".$arrayObj[$clave]->getId_servicio()."' id='bitacora' class='".$claseEstado."' ><p >".$placaVehiculo."<p>
                                </a>";
                                
                                echo "</td>
                                <td >".$razonCliente."</td>
                                <td >".$arrayObj[$clave]->getRuta()."</td>";
                              echo sc_estado_minuta_texto($estado);
                               echo " <td style='width:5%; class='actions'><a href='#' title='".$arrayObj[$clave]->getId_servicio()."' id='bitacora' class='icon'><i class='material-icons'>assignment</i></a></td>
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
          if (window.notificarServiciosVencidos) {
            window.notificarServiciosVencidos(<?php echo $totalVencidos; ?>);
          }

          clearInterval(intervalo);
          var intervalo = setInterval(  function showSeconds(){
              console.log("verificador de estados de minutas ");
              var carga=document.getElementById("cargar");
              if(typeof carga == "undefined" ){
               console.log("carga fallida ");
              }else{
                if(document.getElementById("cargar").value == "carga" ){
                  console.log("carga reactiva funcionando");
                  url="administrador.php";
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
