<?php 

require_once ("/home/segcontr/public_html/app/segcontrol/persistencia/DataSource.php");
require_once ("/home/segcontr/public_html/app/segcontrol/model/servicios.php");
require_once ("/home/segcontr/public_html/app/segcontrol/model/minuta.php");
require_once ("/home/segcontr/public_html/app/segcontrol/model/conductor.php");
require_once ("/home/segcontr/public_html/app/segcontrol/model/vehiculo.php");
class minutaDao
{

  public function listaGeneralMinuta(){

    $data_source = new DataSource();
    $data_table = $data_source->ejecutarConsulta("SELECT * FROM `servicio_novedad`");
    $objMinuta = null;
    $arrayMinuta = array();

    foreach ($data_table as $clave => $valor) {
        $objMinuta = new minuta(
        $data_table[$clave]["idservicio_novedad"],
        $data_table[$clave]["id_servicio"],
        $data_table[$clave]["id_empleado"],
        $data_table[$clave]["fecha_novedad"],
        $data_table[$clave]["evento"],
        $data_table[$clave]["observacion"],
        $data_table[$clave]["nota"],
        isset($data_table[$clave]["fecha_envio_correo"]) ? $data_table[$clave]["fecha_envio_correo"] : null,
        isset($data_table[$clave]["estado_envio_correo"]) ? $data_table[$clave]["estado_envio_correo"] : 'NO ENVIADO',
        isset($data_table[$clave]["detalle_envio_correo"]) ? $data_table[$clave]["detalle_envio_correo"] : null);
        array_push($arrayMinuta, $objMinuta);
      }
      return $arrayMinuta;
    }

     public function ultimoID(){
        $data_source = new DataSource();
        $data_table = $data_source->ejecutarConsulta("SELECT * FROM  servicio_novedad ORDER BY  idservicio_novedad DESC ");
        return $data_table[0]["idservicio_novedad"];
  }

    public function listaMinutaEm($id_empleado){

      $data_source = new DataSource();
      $data_table = $data_source->ejecutarConsulta("SELECT * FROM `servicio_novedad` where id_empleado = :id "
      ,array(':id'=>$id_empleado));
      $objMinuta = null;
      $arrayMinuta = array();

      foreach ($data_table as $clave => $valor) {
          $objMinuta = new minuta(
          $data_table[$clave]["idservicio_novedad"],
          $data_table[$clave]["id_servicio"],
          $data_table[$clave]["id_empleado"],
          $data_table[$clave]["fecha_novedad"],
          $data_table[$clave]["evento"],
          $data_table[$clave]["observacion"],
          $data_table[$clave]["nota"],
          isset($data_table[$clave]["fecha_envio_correo"]) ? $data_table[$clave]["fecha_envio_correo"] : null,
          isset($data_table[$clave]["estado_envio_correo"]) ? $data_table[$clave]["estado_envio_correo"] : 'NO ENVIADO',
          isset($data_table[$clave]["detalle_envio_correo"]) ? $data_table[$clave]["detalle_envio_correo"] : null);
          array_push($arrayMinuta, $objMinuta);
        }
        return $arrayMinuta;
      }

      public function listaMinutaId($id_servicio){

        $data_source = new DataSource();

        $data_table = $data_source->ejecutarConsulta("SELECT * FROM `servicio_novedad` where id_servicio = :id order by idservicio_novedad
        desc" ,array( ':id'=>intval($id_servicio)));
        $objMinuta = null;
        $arrayMinuta = array();

        foreach ($data_table as $clave => $valor) {
            $objMinuta = new minuta(
            $data_table[$clave]["idservicio_novedad"],
            $data_table[$clave]["id_servicio"],
            $data_table[$clave]["id_empleado"],
            $data_table[$clave]["fecha_novedad"],
            $data_table[$clave]["evento"],
            $data_table[$clave]["observacion"],
            $data_table[$clave]["nota"],
            isset($data_table[$clave]["fecha_envio_correo"]) ? $data_table[$clave]["fecha_envio_correo"] : null,
            isset($data_table[$clave]["estado_envio_correo"]) ? $data_table[$clave]["estado_envio_correo"] : 'NO ENVIADO',
            isset($data_table[$clave]["detalle_envio_correo"]) ? $data_table[$clave]["detalle_envio_correo"] : null);
            array_push($arrayMinuta, $objMinuta);
          }
          return $arrayMinuta;
        }

        public function registrarMinuta(minuta $minuta){
          $data_source = new DataSource();

          $sql = "INSERT INTO servicio_novedad
            (id_servicio, id_empleado, fecha_novedad, evento, observacion, nota)
            VALUES (:id_servicio,:id_empleado,:fecha_novedad,:evento,:observacion,:nota)";

              $resultado = $data_source->ejecutarActualizacion($sql,array(
              ':id_servicio'=>$minuta->getId_servicio(),
              ':id_empleado'=>$minuta->getId_empleado(),
              ':fecha_novedad'=>$minuta->getFecha_novedad(),
              ':evento'=>$minuta->getEvento(),
              ':observacion'=>$minuta->getObservacion(),
              ':nota'=>$minuta->getNota()
            )
          );

          if (intval($resultado) > 0) {
            return $data_source->ultimoIdInsertado();
          }

          return 0;

        }

        public function actualizarEstadoEnvioCorreo($id_minuta, $estado, $fecha_envio, $detalle=null){
          $data_source = new DataSource();
          $sql = "UPDATE servicio_novedad
                  SET fecha_envio_correo = :fecha_envio,
                      estado_envio_correo = :estado,
                      detalle_envio_correo = :detalle
                  WHERE idservicio_novedad = :id_minuta";
          return $data_source->ejecutarActualizacion($sql,array(
            ':fecha_envio'=>$fecha_envio,
            ':estado'=>$estado,
            ':detalle'=>$detalle,
            ':id_minuta'=>intval($id_minuta)
          ));
        }

        public function ultimaMinutaServicio($id_servicio){
          $data_source = new DataSource();
          $data_table = $data_source->ejecutarConsulta(
            "SELECT idservicio_novedad FROM servicio_novedad
             WHERE id_servicio = :id_servicio
             ORDER BY idservicio_novedad DESC LIMIT 1",
            array(':id_servicio'=>intval($id_servicio))
          );
          return count($data_table) > 0 ? intval($data_table[0]['idservicio_novedad']) : 0;
        }

        public function ultimoCorreoEnviadoServicio($id_servicio){
          $data_source = new DataSource();
          $data_table = $data_source->ejecutarConsulta(
            "SELECT MAX(fecha_envio_correo) AS ultimo_envio
             FROM servicio_novedad
             WHERE id_servicio = :id_servicio AND estado_envio_correo = 'ENVIADO'",
            array(':id_servicio'=>intval($id_servicio))
          );
          return count($data_table) > 0 ? $data_table[0]['ultimo_envio'] : null;
        }

        public function actualizarMinuta(minuta $minuta){
          $data_source = new DataSource();
          $sql = "UPDATE minuta SET
          id_servicio = :id_servicio,
          id_empleado = :id_empleado,
          fecha_novedad = :fecha_novedad,
          evento = :evento,
          observacion = :observacion,
          nota = :nota
          WHERE id_servico_novedad = :id_servico_novedad
          ";
          $resultado = $data_source->ejecutarActualizacion($sql,array(
          ':id_servico_novedad'=>$minuta->getId_servicio_novedad(),
          ':id_servicio'=>$minuta->getId_servicio(),
          ':id_empleado'=>$minuta->getId_empleado(),
          ':fecha_novedad'=>$minuta->getFecha_novedad(),
          ':evento'=>$minuta->getEvento(),
          ':observacion'=>$minuta->getObservacion(),
          ':nota'=>$minuta->getNota()
        )
      );
        return $resultado;
      }

      public function minutaPorId($id_minuta){

       $data_source = new DataSource();

       $data_table = $data_source->ejecutarConsulta("SELECT * FROM `servicio_novedad` where idservicio_novedad = :id order by idservicio_novedad
       desc" ,array( ':id'=>$id_minuta));
       $objMinuta = null;
       $arrayMinuta = array();

       foreach ($data_table as $clave => $valor) {

           $objMinuta = new minuta(
           $data_table[$clave]["idservicio_novedad"],
           $data_table[$clave]["id_servicio"],
           $data_table[$clave]["id_empleado"],
           $data_table[$clave]["fecha_novedad"],
           $data_table[$clave]["evento"],
           $data_table[$clave]["observacion"],
           $data_table[$clave]["nota"],
           isset($data_table[$clave]["fecha_envio_correo"]) ? $data_table[$clave]["fecha_envio_correo"] : null,
           isset($data_table[$clave]["estado_envio_correo"]) ? $data_table[$clave]["estado_envio_correo"] : 'NO ENVIADO',
           isset($data_table[$clave]["detalle_envio_correo"]) ? $data_table[$clave]["detalle_envio_correo"] : null);
           array_push($arrayMinuta, $objMinuta);
         }
         return $arrayMinuta;
       }




  }
  ?>
