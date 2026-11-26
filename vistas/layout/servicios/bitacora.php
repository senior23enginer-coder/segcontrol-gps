<?php require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/empleadoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/minutaController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");

session_start();
$id = $_POST['id'];

$controllerServicio = new serviciosController();
$controllerEmpleado = new empleadoController();
$controllerMinuta=new minutaController();
$controllerCliente=new clienteController();

$objServicio=$controllerServicio->serviciosPorId($id);
$objEmpleado=$controllerEmpleado->empleadoId($objServicio->getId_empleado());

$arrayMinuta= $controllerMinuta->minutasPorServicios($id);
$ultimoCorreoEnviado = $controllerMinuta->ultimoCorreoEnviadoServicio($id);

$objCliente = $controllerCliente->clienteId($objServicio->getId_cliente());

setlocale(LC_ALL,"es_ES");
ini_set('date.timezone','America/Bogota');
date("Y-m-d H:i:s");
$fecha = date("Y-m-d H:i:s");
?>

<input id="idempleadoHidden" type="hidden" name="idempleadoHidden" value="<?php  echo $objEmpleado->getId_empleado(); ?>" />
<input id="idservicioHidden" type="hidden" name="idservicioHidden" value="<?php   echo $objServicio->getId_servicio(); ?>" />
<div class="row clearfix">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
      <div class="header">
        <h2>Descripcion del Servicio : CLIENTE <?php   echo $objEmpleado->getNombre();?> </h2> Registro de fecha y hora del servicio &nbsp;&nbsp;<?php   echo $objServicio->getFecha_creacion();?>
      </div>
      <div class="service-action-bar">
        <button type="button" id="nuevaMinuta" class="btn btn-raised btn-success waves-effect">
          <i class="material-icons">note_add</i>
          Crear Minuta
        </button>
        <button type="button" id="verPdfServicio" class="btn btn-raised btn-info waves-effect">
          <i class="material-icons">picture_as_pdf</i>
          Ver PDF
        </button>
        <button type="button" class="btn btn-raised btn-primary waves-effect reenviarCorreoServicio">
          <i class="material-icons">email</i>
          Enviar correo nuevamente
        </button>
        <button type="button" id="cerrarServicio" class="btn btn-raised btn-danger waves-effect">
          <i class="material-icons">lock</i>
          Cerrar Servicio
        </button>
      </div>
      <div class="body">
        <small class="text-muted">Configuracion de ciudad de origen y destino</small>
        <div class="row clearfix">
          <div class="col-sm-4">
            <div class="form-group">
              <div class="form-line">
                <small class="text-muted">Ciudad de Origen </small>
                <input type="text" class="form-control" name="municipio" id="municipio" value="<?php   echo $objServicio->getCiudad_origen();?>">
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <div class="form-line">
                <small class="text-muted">Ciudad de Destino </small>
                <input type="text" class="form-control" name="municipio2" id="municipio2" value="<?php   echo $objServicio->getCiudad_destino();?>">
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <div class="form-line">
                <small class="text-muted">Sello </small>
                <input type="text" class="form-control" name="sello" id="sello" value="<?php   echo $objServicio->getSello();?>">
              </div>
            </div>
          </div>
        </div>

        <div class="row clearfix">
          <div class="col-sm-6">
            <div class="form-group">
              <div class="form-line">
                <small class="text-muted">Ruta asignada</small>
                <input type="text" class="form-control" name="ruta" id="ruta" value="<?php   echo $objServicio->getRuta();?>" >
            </div>
          </div>
         </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="form-line">
                <small class="text-muted">Satelital activo</small>
                <input type="text" class="form-control" name="satelital" id="satelital" value="<?php   echo $objServicio->getSatelital();?>">
            </div>
          </div>
        </div>
    </div>
    <div class="row clearfix">
      <div class="col-sm-4">
        <div class="form-group">
          <div class="form-line">
            <small class="text-muted">Nombre del cliente</small>
            <input type="text" class="form-control" name="municipio" id="municipio" value="<?php   echo $objCliente->getNombre();?>">
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <div class="form-line">
            <small class="text-muted">Razon social </small>
            <input type="text" class="form-control" name="municipio2" id="municipio2" value="<?php   echo $objCliente->getRazon_social();?>">
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <div class="form-line">
            <small class="text-muted">Numero de contacto </small>
            <input type="text" class="form-control" name="sello" id="sello" value="<?php   echo $objCliente->getFijo();?>">
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <div class="form-line">
            <small class="text-muted">Numero de contacto #2</small>
            <input type="text" class="form-control" name="sello" id="sello" value="<?php   echo $objCliente->getCelular1();?>">
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <div class="form-line">
            <small class="text-muted">Numero de contacto #3</small>
            <input type="text" class="form-control" name="sello" id="sello" value="<?php   echo $objCliente->getCelular2();?>">
          </div>
        </div>
      </div>
    </div>
    <div class="row clearfix service-evidence-grid">
        <div class="col-sm-6">
         <div class="service-evidence-card">
          <div class="service-evidence-title">Foto parte frontal</div>
          <div class="service-evidence-frame">
           <img class="service-evidence-image" src="<?php   echo "../../../app/servicios/".$objServicio->getId_servicio()."/carro1.jpg";  ?>" alt="Evidencia frontal del servicio">
          </div>
         </div>
        </div>
        <div class="col-sm-6">
         <div class="service-evidence-card">
          <div class="service-evidence-title">Foto parte posterior</div>
          <div class="service-evidence-frame">
           <img class="service-evidence-image" src="<?php   echo "../../../app/servicios/".$objServicio->getId_servicio()."/carro2.jpg";  ?>" alt="Evidencia posterior del servicio">
          </div>
         </div>
        </div>
    </div>
  </div>
</div>
</div>
</div>
<div class="row clearfix">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
      <div class="header">
        <h3 class="font-bold col-teal">Registro y Seguimiento . </h3>
      </div>

      <div class="body">
        <small class="text-muted"><?php  echo " ".$sesion_rol;  ?> Seguimientos.</small>
        <small class="text-muted seguimiento-ultimo-correo">
          Ultimo correo enviado:
          <strong><?php echo $ultimoCorreoEnviado ? htmlspecialchars($ultimoCorreoEnviado, ENT_QUOTES, 'UTF-8') : 'Sin envios registrados'; ?></strong>
        </small>
      <?php 
echo "<div class='row clearfix '>
           <div class='col-xs-12 col-sm-12 col-md-12'>
               <div class='body table-responsive'>";
      echo "<div class='service-action-bar service-action-bar-secondary'>
              <button type='button' id='nuevaMinuta' class='btn btn-raised btn-success waves-effect'>
                <i class='material-icons'>note_add</i>
                Crear Minuta
              </button>
              <button type='button' id='verPdfServicio' class='btn btn-raised btn-info waves-effect'>
                <i class='material-icons'>picture_as_pdf</i>
                Ver PDF
              </button>
              <button type='button' class='btn btn-raised btn-primary waves-effect reenviarCorreoServicio'>
                <i class='material-icons'>email</i>
                Enviar correo nuevamente
              </button>
              <button type='button' id='cerrarServicio' class='btn btn-raised btn-danger waves-effect'>
                <i class='material-icons'>lock</i>
                Cerrar Servicio
              </button>
            </div>";
      echo   "  <div id='tabla'>
                 <table class='table table-striped table-borderless'>
                   <thead>
                     <tr>
                       <th>Fecha novedad</th>
                       <th>Fecha hora envio de correo</th>
                       <th>Estado del envio</th>
                       <th>Evento</th>
                       <th>Observacion</th>
                       <th style='width:5%;' class='actions'>Ver minuta</th>
                     </tr>
                    </thead>
                   <tbody class='no-border-x'>";
                         foreach ($arrayMinuta as $clave => $valor) {
               echo "<tr>
                         <td>".htmlspecialchars($arrayMinuta[$clave]->getFecha_novedad(), ENT_QUOTES, 'UTF-8')."</td>
                         <td>".($arrayMinuta[$clave]->getFecha_envio_correo() ? htmlspecialchars($arrayMinuta[$clave]->getFecha_envio_correo(), ENT_QUOTES, 'UTF-8') : 'Sin envio')."</td>
                         <td>".($arrayMinuta[$clave]->getEstado_envio_correo() === 'ENVIADO'
                           ? "<span class='label label-success'>ENVIADO</span>"
                           : "<span tabindex='0' class='label label-danger estado-correo-tooltip' data-motivo='".htmlspecialchars($arrayMinuta[$clave]->getDetalle_envio_correo() ? $arrayMinuta[$clave]->getDetalle_envio_correo() : 'Sin diagnostico registrado.', ENT_QUOTES, 'UTF-8')."'>NO ENVIADO</span>")."</td>
                         <td>".htmlspecialchars($arrayMinuta[$clave]->getEvento(), ENT_QUOTES, 'UTF-8')."</td>
                         <td>".htmlspecialchars($arrayMinuta[$clave]->getObservacion(), ENT_QUOTES, 'UTF-8')."</td>
                         <td style='width:5%;' class='actions'><a href='#' title='".$arrayMinuta[$clave]->getId_servicio_novedad()."' id='verMinuta' class='icon'><i class='zmdi zmdi-comment-text-alt'></i></a></td>
                     </tr>";}
             echo " </tbody>
                 </table>
                 </div>
               </div>
            </div>
          </div>";
        ?>
      </div>
    </div>
  </div>
</div>
<div id="smg_bitacora"></div>
