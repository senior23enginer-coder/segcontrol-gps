<?php 

require_once("/home/segcontr/public_html/app/segcontrol/configuracion/ParametrosSistema.php");
class DataSource
{
	private $cadenaConexion;
	private $conexion;
	private static $conexionCompartida = null;
	private static $cadenaConexionCompartida = "";
	private static $cacheConsultas = array();
	private static $ultimoErrorConexion = "";

	public function __construct(){
		try{
			$Parameter = new ParametrosSistema();
			$this->cadenaConexion = "mysql:host=".$Parameter->getIPBD().";dbname=".$Parameter->getNombreBD().";charset=utf8";
			if (!is_null(self::$conexionCompartida) && self::$cadenaConexionCompartida === $this->cadenaConexion) {
				$this->conexion = self::$conexionCompartida;
				return;
			}

			$this->conexion = new PDO(
				$this->cadenaConexion,
				$Parameter->getUsuarioBD(),
				$Parameter->getClaveBD(),
				array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES => false
				)
			);
			self::$conexionCompartida = $this->conexion;
			self::$cadenaConexionCompartida = $this->cadenaConexion;
			self::$ultimoErrorConexion = "";

		}catch(PDOException $e){
			$this->conexion = null;
			self::$ultimoErrorConexion = $e->getMessage();
			error_log("Error de conexion a base de datos: ".$e->getMessage());
		}
	}

	public function estaConectado(){
		return !is_null($this->conexion);
	}

	public static function getUltimoErrorConexion(){
		return self::$ultimoErrorConexion;
	}

	public function ejecutarConsulta($sql,$values = array()){
		if($sql == "" || is_null($this->conexion)){
			return array();
		}

		try{
			$cacheKey = md5($sql.serialize($values));
			if (isset(self::$cacheConsultas[$cacheKey])) {
				return self::$cacheConsultas[$cacheKey];
			}

			$consulta = $this->conexion->prepare($sql);
			$consulta->execute($values);
			$resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
			self::$cacheConsultas[$cacheKey] = $resultado;
			return $resultado;
		}catch(PDOException $e){
			error_log("Error ejecutando consulta: ".$e->getMessage()." SQL: ".$sql);
			return array();
		}
	}

	public function ejecutarActualizacion($sql ,$values = array()){
		if($sql == "" || is_null($this->conexion)){
			return 0;
		}

		try{
			$consulta = $this->conexion->prepare($sql);
			$consulta->execute($values);
			$numero_tablas_afectadas = $consulta->rowCount();
			self::$cacheConsultas = array();
			return $numero_tablas_afectadas;
		}catch(PDOException $e){
			error_log("Error ejecutando actualizacion: ".$e->getMessage()." SQL: ".$sql);
			return 0;
		}
	}

	public function ultimoIdInsertado(){
		if(is_null($this->conexion)){
			return 0;
		}

		return intval($this->conexion->lastInsertId());
	}
}
?>
