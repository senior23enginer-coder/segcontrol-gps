<?php header("access-control-allow-origin: *"); 
session_start();

require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/utilidades/mail.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/minutaController.php");
require_once ("/home/segcontr/public_html/app/segcontrol/persistencia/DataSource.php");
require_once ("/home/segcontr/public_html/app/segcontrol/persistencia/consultaClienteServicioDao.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");

function responderServicio($tipo, $mensaje)
{
  echo '<div class="alert alert-' . $tipo . '">
           <strong>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</strong>
        </div>';
}

function directorioServicios()
{
  $appRoot = realpath(__DIR__ . '/../../../../');
  if ($appRoot === false) {
    return false;
  }

  return $appRoot . DIRECTORY_SEPARATOR . 'servicios';
}

function asegurarDirectorioServicio($idservicio)
{
  $baseServicios = directorioServicios();
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

function moverImagenServicio($input, $directorio, $nombreArchivo)
{
  if (!isset($_FILES[$input]) || !is_uploaded_file($_FILES[$input]['tmp_name'])) {
    return false;
  }

  return @move_uploaded_file(
    $_FILES[$input]['tmp_name'],
    $directorio . DIRECTORY_SEPARATOR . $nombreArchivo
  );
}


$idEmpleado=1;

if (session_status()==2){
  if(isset($_SESSION['id']))
     {
     $idEmpleado=$_SESSION['id'];
     }

}
$controller_servicio = new serviciosController();
$model_servicios = new servicio(
"NULL"  ,
$_POST['cb3'],
$_POST['cb2'],
$_POST['cb1'],
$idEmpleado,
$_POST['manifiesto'],
$_POST['fecha'],
" ",
"ACTIVO",
$_POST['satelital'],
$_POST['municipio'],
$_POST['municipio2'],
$_POST['direccion'],
$_POST['sello'],
$_POST['ncontenedor'],
$_POST['link'],
$_POST['usuario'],
$_POST['clave'],
$_POST['ruta']
);
if($controller_servicio->registroServicio($model_servicios) > 0){


       $idservicio =$controller_servicio->ultimoID();
       $directorio = asegurarDirectorioServicio($idservicio);

    if ($directorio === false) {
      responderServicio('warning', 'El servicio fue creado, pero no se pudo crear el directorio de evidencias. Verifica permisos de escritura en /public_html/app/servicios.');
      exit;
    }

    $f1 = moverImagenServicio('file-input1', $directorio, 'carro1.jpg');
    $f2 = moverImagenServicio('file-input2', $directorio, 'carro2.jpg');

    if (!$f1 || !$f2) {
      responderServicio('warning', 'El servicio fue creado, pero no se pudieron guardar todas las imagenes. Verifica permisos y archivos cargados.');
      exit;
    }

    responderServicio('success', 'Registro de servicio completo.');

}else{
  responderServicio('danger', 'No se puede registrar el nuevo servicio.');
}
?>
