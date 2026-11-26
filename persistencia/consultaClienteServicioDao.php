<?php require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/utilidades/generarPDF.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/minutaController.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once("/home/segcontr/public_html/app/segcontrol/persistencia/minutaDao.php");
require_once ("/home/segcontr/public_html/app/segcontrol/persistencia/DataSource.php");

class consultaClienteServicioDao{

public  function servicioMinuta($id){


  $controller_servicio = new serviciosController();
  $objServicio=$controller_servicio->serviciosPorId($id);
  
  $data_source = new DataSource();
  $data_table = $data_source->ejecutarConsulta(
    "SELECT  nov.idservicio_novedad, nov.fecha_novedad, nov.evento, nov.observacion, nov.nota
        FROM servicio join servicio_novedad as nov
                          on(servicio.id_servicio=nov.id_servicio)
        where servicio.id_servicio = ?
        order by nov.idservicio_novedad DESC",
    array($objServicio->getId_servicio()));

  $servicio = $data_source->ejecutarConsulta(
    "SELECT servicio.id_servicio, servicio.ruta, servicio.numero_contenedor, servicio.manifiesto,
    cliente.razon_social, cliente.nombre_contacto,
 cliente.celular1, conductor.nombre_conductor, conductor.cedula, conductor.telefono1,
 vehiculo.placa, vehiculo.marca, vehiculo.color
 FROM servicio  join cliente
 on(cliente.id_cliente=servicio.id_cliente) join conductor  
 on(conductor.id_conductor=servicio.id_conductor) join vehiculo 
 on(vehiculo.id_vehiculo=servicio.id_vehiculo)
 where servicio.id_servicio = ?",
    array($objServicio->getId_servicio()));
  if (empty($servicio)) {
    return "";
  }
  $generador=new GeneradorArchivo();
  $generador->procesar($servicio,$data_table);
 
  return $servicio[0]['placa']; 

 }

}
?>
