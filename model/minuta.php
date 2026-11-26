<?php 


class minuta{

  private $id_servicio_novedad;
  private $id_servicio;
  private $id_empleado;
  private $fecha_novedad;
  private $fecha_envio_correo;
  private $estado_envio_correo;
  private $detalle_envio_correo;
  private $evento;
  private $observacion;
  private $nota;

  public function __construct($id_servicio_novedad,$id_servicio,$id_empleado,$fecha_novedad,$evento,$observacion,$nota,$fecha_envio_correo=null,$estado_envio_correo='NO ENVIADO',$detalle_envio_correo=null){
    $this->id_servicio_novedad=$id_servicio_novedad;
    $this->id_servicio=$id_servicio;
    $this->id_empleado=$id_empleado;
    $this->fecha_novedad=$fecha_novedad;
    $this->fecha_envio_correo=$fecha_envio_correo;
    $this->estado_envio_correo=$estado_envio_correo;
    $this->detalle_envio_correo=$detalle_envio_correo;
    $this->evento=$evento;
    $this->observacion=$observacion;
    $this->nota=$nota;
  }


  public function setId_servicio_novedad($id_servicio_novedad){$this->id_servicio_novedad = $id_servicio_novedad;}
  public function getId_servicio_novedad(){return $this->id_servicio_novedad;}

  public function setId_servicio($id_servicio){$this->id_servicio = $id_servicio;}
  public function getId_servicio(){return $this->id_servicio;}

  public function setId_empleado($id_empleado){$this->id_empleado = $id_empleado;}
  public function getId_empleado(){return $this->id_empleado;}

  public function setFecha_novedad($fecha_novedad){$this->fecha_novedad = $fecha_novedad;}
  public function getFecha_novedad(){return $this->fecha_novedad;}

  public function setFecha_envio_correo($fecha_envio_correo){$this->fecha_envio_correo = $fecha_envio_correo;}
  public function getFecha_envio_correo(){return $this->fecha_envio_correo;}

  public function setEstado_envio_correo($estado_envio_correo){$this->estado_envio_correo = $estado_envio_correo;}
  public function getEstado_envio_correo(){return $this->estado_envio_correo;}

  public function setDetalle_envio_correo($detalle_envio_correo){$this->detalle_envio_correo = $detalle_envio_correo;}
  public function getDetalle_envio_correo(){return $this->detalle_envio_correo;}

  public function setEvento($evento){$this->evento = $evento;}
  public function getEvento(){return $this->evento;}

  public function setObservacion($observacion){$this->observacion = $observacion;}
  public function getObservacion(){return $this->observacion;}

  public function setNota($nota){$this->nota = $nota;}
  public function getNota(){return $this->nota;}



}



?>
