<?php header("access-control-allow-origin: *"); 


require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/utilidades/mail.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/correoServiciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/correoServicios.php");

require_once("/home/segcontr/public_html/app/segcontrol/controller/minutaController.php");
require_once ("/home/segcontr/public_html/app/segcontrol/persistencia/DataSource.php");
require_once ("/home/segcontr/public_html/app/segcontrol/persistencia/consultaClienteServicioDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");

date_default_timezone_set('America/Bogota');

function responderMinuta($tipo, $mensaje)
{
  echo '<div class="alert alert-' . $tipo . '">
              <strong>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</strong>
           </div>';
}

function registrarResultadoCorreoMinuta($controller, $idMinuta, $enviado, $detalle)
{
  $estado = $enviado ? 'ENVIADO' : 'NO ENVIADO';
  $controller->actualizarEstadoEnvioCorreo($idMinuta, $estado, date('Y-m-d H:i:s'), $detalle);
}

function directorioServiciosMinuta()
{
  $appRoot = realpath(__DIR__ . '/../../../../');
  if ($appRoot === false) {
    return false;
  }

  return $appRoot . DIRECTORY_SEPARATOR . 'servicios';
}

function asegurarDirectorioMinuta($idservicio)
{
  $baseServicios = directorioServiciosMinuta();
  if ($baseServicios === false) {
    return false;
  }

  if (!is_dir($baseServicios) && !@mkdir($baseServicios, 0775, true)) {
    return false;
  }

  $directorio = $baseServicios . DIRECTORY_SEPARATOR . intval($idservicio);
  if (!is_dir($directorio) && !@mkdir($directorio, 0775, true)) {
    return false;
  }

  return $directorio;
}

function archivoMinutaValido($input)
{
  if (!isset($_FILES[$input]) || !isset($_FILES[$input]['error'])) {
    return false;
  }

  if ($_FILES[$input]['error'] !== UPLOAD_ERR_OK) {
    return false;
  }

  return is_uploaded_file($_FILES[$input]['tmp_name']);
}

function archivoMinutaFueEnviado($input)
{
  return isset($_FILES[$input]) &&
    isset($_FILES[$input]['error']) &&
    $_FILES[$input]['error'] !== UPLOAD_ERR_NO_FILE;
}

  $controller_minuta = new minutaController();
  $idservicio = $_POST['idservicio'];
  $idempleado = $_POST['idempleado'];

  $model_minuta = new minuta('NULL',
    $idservicio,
    $idempleado,
    $_POST['fecha_novedad'],
    $_POST['evento'],
    $_POST['observacion'],
    $_POST['nota']
  );

   $id = $controller_minuta->registrarMinuta($model_minuta);
   if(intval($id) > 0){

    if (archivoMinutaFueEnviado('file-input1')) {
      if (!archivoMinutaValido('file-input1')) {
        responderMinuta('warning', 'La minuta fue creada, pero la imagen no se recibio correctamente. Verifica que el archivo no supere el limite permitido.');
        exit;
      }

      $directorio = asegurarDirectorioMinuta($idservicio);
      if ($directorio === false) {
        responderMinuta('warning', 'La minuta fue creada, pero no se pudo crear el directorio de evidencias. Verifica permisos de escritura en /public_html/app/servicios.');
        exit;
      }

      $temp_archivo2 = $_FILES["file-input1"]["tmp_name"];
      $f2=@move_uploaded_file($temp_archivo2,$directorio . DIRECTORY_SEPARATOR .$id.".jpg");
      if (!$f2) {
        responderMinuta('warning', 'La minuta fue creada, pero no se pudo guardar la imagen de evidencia.');
        exit;
      }
    }

    $o = new  consultaClienteServicioDao();
    $controller_servicio = new serviciosController();
    $servicio=$controller_servicio->serviciosPorId($idservicio);
    $placa=$o->servicioMinuta($idservicio);
    $ObjCliente=new clienteController();
    $ob=$ObjCliente->clienteId(intval($servicio->getId_cliente()));
    $objCorreo = new correoServiciosController();
    $correoActual=$objCorreo->CorreoServiciosIdActivacion();
    if (!$correoActual) {
      registrarResultadoCorreoMinuta($controller_minuta, $id, false, 'No hay un correo remitente activo configurado.');
      responderMinuta('warning', 'La minuta fue creada, pero no hay un correo remitente activo configurado para enviar la notificacion.');
      exit;
    }

      $m = new mail();
      $resultadoCorreo = $m->correo(
      $ob,
     "operaciones.segcontrol@gmail.com",
     "segcontrolgps@gmail.com",
     "Reporte de Minuta Vehiculo (" .$placa .")",
     "Reporte de Minuta Vehiculo (" .$placa .")",
     $correoActual->getCorreo(),
     $correoActual->getClave(),$idservicio
   );

    if (!is_array($resultadoCorreo) || !$resultadoCorreo['ok']) {
      $detalleCorreo = is_array($resultadoCorreo) && isset($resultadoCorreo['detalle']) ? $resultadoCorreo['detalle'] : '';
      $mensajeCorreo = is_array($resultadoCorreo) && isset($resultadoCorreo['mensaje']) ? $resultadoCorreo['mensaje'] : 'La minuta fue creada, pero no se pudo confirmar el envio del correo.';
      registrarResultadoCorreoMinuta($controller_minuta, $id, false, $mensajeCorreo . ($detalleCorreo !== '' ? ' Detalle: ' . $detalleCorreo : ''));
      responderMinuta('warning', $mensajeCorreo . ($detalleCorreo !== '' ? ' Detalle: ' . $detalleCorreo : ''));
      exit;
    }

    registrarResultadoCorreoMinuta($controller_minuta, $id, true, 'Correo enviado correctamente.');
    responderMinuta('success', 'Registro de minuta completa. Correo enviado correctamente.');
  }
   else{
     responderMinuta('danger', 'No se puede registrar la minuta actual.');
   }
 ?>
