<?php

require_once("/home/segcontr/public_html/app/segcontrol/lib/PDF/src/Cezpdf.php");
require_once("/home/segcontr/public_html/app/segcontrol/model/servicios.php");

class GeneradorArchivo
{
  public function __construct(){}

  public function puntos_cm($medida, $resolucion = 72)
  {
    return ($medida / 2.54) * $resolucion;
  }

  private function valor($valor)
  {
    $valor = trim((string)$valor);
    return $valor === "" ? "N/A" : $valor;
  }

  private function colorHex($pdf, $hex, $stroke = false)
  {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;

    if ($stroke) {
      $pdf->setStrokeColor($r, $g, $b);
      return;
    }

    $pdf->setColor($r, $g, $b);
  }

  private function texto($pdf, $x, $y, $tamano, $texto, $color = '#111827')
  {
    $this->colorHex($pdf, $color);
    $pdf->addText($x, $y, $tamano, $texto);
    $this->colorHex($pdf, '#000000');
  }

  private function tituloSeccion($pdf, $x, $y, $titulo)
  {
    $this->colorHex($pdf, '#0f3558');
    $pdf->filledRectangle($x, $y - 5, 4, 18);
    $this->texto($pdf, $x + 12, $y, 12, "<b>".$titulo."</b>", '#0f172a');
  }

  private function addImagenCaja($pdf, $ruta, $x, $y, $ancho, $alto, $modo = 'contain')
  {
    $this->colorHex($pdf, '#f8fafc');
    $pdf->filledRectangle($x, $y, $ancho, $alto);
    $this->colorHex($pdf, '#d8e1ec', true);
    $pdf->rectangle($x, $y, $ancho, $alto);

    if (!is_file($ruta)) {
      error_log("PDF imagen no encontrada: ".$ruta);
      $this->texto($pdf, $x + 22, $y + ($alto / 2), 9, "Imagen no disponible", '#64748b');
      return;
    }

    $info = @getimagesize($ruta);
    if (!$info || $info[0] <= 0 || $info[1] <= 0) {
      error_log("PDF imagen invalida: ".$ruta);
      $this->texto($pdf, $x + 22, $y + ($alto / 2), 9, "Imagen no disponible", '#64748b');
      return;
    }

    if ($modo === 'cover') {
      $rutaPdf = $this->normalizarImagenCubiertaParaPdf($ruta, $info, $ancho, $alto);
      $w = $ancho - 2;
      $h = $alto - 2;
      $ix = $x + 1;
      $iy = $y + 1;
    } else {
      $escala = min(($ancho - 2) / $info[0], ($alto - 2) / $info[1]);
      $w = $info[0] * $escala;
      $h = $info[1] * $escala;
      $ix = $x + (($ancho - $w) / 2);
      $iy = $y + (($alto - $h) / 2);
      $rutaPdf = $this->normalizarImagenParaPdf($ruta, $info);
    }

    if ($rutaPdf === false) {
      error_log("PDF imagen no compatible: ".$ruta);
      $this->texto($pdf, $x + 22, $y + ($alto / 2), 9, "Imagen no disponible", '#64748b');
      return;
    }

    $pdf->addJpegFromFile($rutaPdf, $ix, $iy, $w, $h);
  }

  private function normalizarImagenCubiertaParaPdf($ruta, $info, $anchoCaja, $altoCaja)
  {
    if (!function_exists('imagecreatefromstring')) {
      return $this->normalizarImagenParaPdf($ruta, $info);
    }

    $contenido = @file_get_contents($ruta);
    if ($contenido === false) {
      return false;
    }

    $origen = @imagecreatefromstring($contenido);
    if (!$origen) {
      return false;
    }

    $anchoOrigen = imagesx($origen);
    $altoOrigen = imagesy($origen);
    if ($anchoOrigen <= 0 || $altoOrigen <= 0) {
      imagedestroy($origen);
      return false;
    }

    $ratioCaja = $anchoCaja / $altoCaja;
    $ratioOrigen = $anchoOrigen / $altoOrigen;

    if ($ratioOrigen > $ratioCaja) {
      $altoRecorte = $altoOrigen;
      $anchoRecorte = intval($altoOrigen * $ratioCaja);
      $xRecorte = intval(($anchoOrigen - $anchoRecorte) / 2);
      $yRecorte = 0;
    } else {
      $anchoRecorte = $anchoOrigen;
      $altoRecorte = intval($anchoOrigen / $ratioCaja);
      $xRecorte = 0;
      $yRecorte = intval(($altoOrigen - $altoRecorte) / 2);
    }

    $destino = imagecreatetruecolor(max(1, intval($anchoCaja * 3)), max(1, intval($altoCaja * 3)));
    imagecopyresampled(
      $destino,
      $origen,
      0,
      0,
      $xRecorte,
      $yRecorte,
      imagesx($destino),
      imagesy($destino),
      $anchoRecorte,
      $altoRecorte
    );

    $temporal = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'segcontrol_pdf_cover_' . md5($ruta . filemtime($ruta) . $anchoCaja . $altoCaja) . '.jpg';
    $ok = @imagejpeg($destino, $temporal, 92);
    imagedestroy($origen);
    imagedestroy($destino);

    return $ok && is_file($temporal) ? $temporal : false;
  }

  private function addImagenCubierta($pdf, $ruta, $x, $y, $ancho, $alto)
  {
    if (!is_file($ruta)) {
      return;
    }

    $info = @getimagesize($ruta);
    if (!$info || $info[0] <= 0 || $info[1] <= 0) {
      return;
    }

    $rutaPdf = $this->normalizarImagenParaPdf($ruta, $info);
    if ($rutaPdf !== false) {
      $pdf->addJpegFromFile($rutaPdf, $x, $y, $ancho, $alto);
    }
  }

  private function normalizarImagenParaPdf($ruta, $info)
  {
    if ($info[2] === IMAGETYPE_JPEG && $this->jpegCompatible($ruta)) {
      return $ruta;
    }

    if (!function_exists('imagecreatefromstring')) {
      return $info[2] === IMAGETYPE_JPEG ? $ruta : false;
    }

    $contenido = @file_get_contents($ruta);
    if ($contenido === false) {
      return false;
    }

    $imagen = @imagecreatefromstring($contenido);
    if (!$imagen) {
      return false;
    }

    $temporal = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'segcontrol_pdf_' . md5($ruta . filemtime($ruta)) . '.jpg';
    $ok = @imagejpeg($imagen, $temporal, 90);
    imagedestroy($imagen);

    return $ok && is_file($temporal) ? $temporal : false;
  }

  private function jpegCompatible($ruta)
  {
    $contenido = @file_get_contents($ruta, false, null, 0, 2048);
    if ($contenido === false) {
      return false;
    }

    return strpos($contenido, 'Adobe') === false;
  }

  private function resolverImagen($directorios, $nombreBase)
  {
    if (!is_array($directorios)) {
      $directorios = array($directorios);
    }

    $extensiones = array('.jpg', '.jpeg', '.png', '.gif', '.webp', '.JPG', '.JPEG', '.PNG', '.GIF', '.WEBP');
    foreach ($directorios as $directorio) {
      foreach ($extensiones as $extension) {
        $ruta = $directorio . DIRECTORY_SEPARATOR . $nombreBase . $extension;
        if (is_file($ruta)) {
          return $ruta;
        }
      }
    }

    $rutaJpg = $directorios[0] . DIRECTORY_SEPARATOR . $nombreBase . '.jpg';
    return $rutaJpg;
  }

  private function resolverImagenMinutaDisponible($directorios, $datos)
  {
    if (empty($datos)) {
      return "";
    }

    foreach ($datos as $novedad) {
      if (!isset($novedad['idservicio_novedad'])) {
        continue;
      }

      $ruta = $this->resolverImagen($directorios, $novedad['idservicio_novedad']);
      if (is_file($ruta)) {
        return $ruta;
      }
    }

    $imagenPorCarpeta = $this->resolverUltimaImagenNumerica($directorios);
    if ($imagenPorCarpeta !== "") {
      return $imagenPorCarpeta;
    }

    return $this->resolverImagen($directorios, $datos[0]['idservicio_novedad']);
  }

  private function resolverUltimaImagenNumerica($directorios)
  {
    if (!is_array($directorios)) {
      $directorios = array($directorios);
    }

    $ultimaRuta = "";
    $ultimaFecha = 0;
    foreach ($directorios as $directorio) {
      if (!is_dir($directorio)) {
        continue;
      }

      $archivos = glob($directorio . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
      if (!$archivos) {
        continue;
      }

      foreach ($archivos as $archivo) {
        $nombre = pathinfo($archivo, PATHINFO_FILENAME);
        if (!ctype_digit((string)$nombre)) {
          continue;
        }

        $fecha = @filemtime($archivo);
        if ($fecha !== false && $fecha > $ultimaFecha) {
          $ultimaFecha = $fecha;
          $ultimaRuta = $archivo;
        }
      }
    }

    return $ultimaRuta;
  }

  private function dibujarPie($pdf)
  {
    $this->colorHex($pdf, '#64748b');
    $pdf->addText(40, 24, 8, "SEGCONTROL GPS - Informe generado automaticamente");
    $pdf->addText(468, 24, 8, "Documento confidencial");
    $this->colorHex($pdf, '#000000');
  }

  private function tablaDetalle($pdf, $data, $y)
  {
    $pdf->ezSetY($y);
    $pdf->ezTable(
      $data,
      array('campo' => '', 'valor' => '', 'campo2' => '', 'valor2' => ''),
      '',
      array(
        'showHeadings' => 0,
        'shaded' => 1,
        'shadeCol' => array(0.96, 0.98, 1),
        'shadeCol2' => array(1, 1, 1),
        'lineCol' => array(0.82, 0.87, 0.92),
        'innerLineThickness' => 0.4,
        'outerLineThickness' => 0.6,
        'fontSize' => 9,
        'rowGap' => 4,
        'colGap' => 6,
        'width' => 505,
        'cols' => array(
          'campo' => array('width' => 74),
          'valor' => array('width' => 180),
          'campo2' => array('width' => 74),
          'valor2' => array('width' => 177)
        )
      )
    );
  }

  private function textoCortado($valor, $maximo)
  {
    $valor = trim((string)$valor);
    if (strlen($valor) <= $maximo) {
      return $valor;
    }

    return substr($valor, 0, $maximo - 3) . '...';
  }

  private function dividirTexto($valor, $maximo)
  {
    $valor = trim((string)$valor);
    if ($valor === "") {
      return array("");
    }

    $lineas = array();
    $partes = preg_split('/\r\n|\r|\n/', $valor);
    foreach ($partes as $parte) {
      $parte = trim($parte);
      if ($parte === "") {
        $lineas[] = "";
        continue;
      }

      $bloque = wordwrap($parte, $maximo, "\n", false);
      $bloqueLineas = explode("\n", $bloque);
      foreach ($bloqueLineas as $linea) {
        $lineas[] = trim($linea);
      }
    }

    return $lineas;
  }

  private function dibujarEncabezadoHistorial($pdf, $id)
  {
    $this->colorHex($pdf, '#ffffff');
    $pdf->filledRectangle(0, 0, 595, 842);
    $this->colorHex($pdf, '#0f3558');
    $pdf->filledRectangle(0, 800, 595, 42);
    $this->texto($pdf, 40, 815, 13, "<b>HISTORIAL DE EVENTOS</b>", '#ffffff');
    $this->texto($pdf, 425, 815, 9, "Servicio ID: ".$id, '#dbeafe');
  }

  private function dibujarEncabezadoTablaHistorial($pdf, $x, $y, $anchoFecha, $anchoEvento, $anchoObservacion)
  {
    $altoEncabezado = 22;
    $anchoTotal = $anchoFecha + $anchoEvento + $anchoObservacion;

    $this->colorHex($pdf, '#0f3558');
    $pdf->filledRectangle($x, $y, $anchoTotal, $altoEncabezado);
    $this->colorHex($pdf, '#244d73', true);
    $pdf->rectangle($x, $y, $anchoTotal, $altoEncabezado);
    $pdf->line($x + $anchoFecha, $y, $x + $anchoFecha, $y + $altoEncabezado);
    $pdf->line($x + $anchoFecha + $anchoEvento, $y, $x + $anchoFecha + $anchoEvento, $y + $altoEncabezado);

    $this->texto($pdf, $x + 8, $y + 7, 9, "<b>Fecha / Hora</b>", '#ffffff');
    $this->texto($pdf, $x + $anchoFecha + 8, $y + 7, 9, "<b>Evento</b>", '#ffffff');
    $this->texto($pdf, $x + $anchoFecha + $anchoEvento + 8, $y + 7, 9, "<b>Observacion</b>", '#ffffff');
  }

  private function dibujarHistorialEventos($pdf, $data, $id)
  {
    $x = 55;
    $y = 760;
    $altoEncabezado = 22;
    $altoLinea = 11;
    $anchoFecha = 105;
    $anchoEvento = 150;
    $anchoObservacion = 250;
    $anchoTotal = $anchoFecha + $anchoEvento + $anchoObservacion;

    $this->dibujarEncabezadoTablaHistorial($pdf, $x, $y, $anchoFecha, $anchoEvento, $anchoObservacion);
    $filaY = $y - $altoEncabezado;
    for ($i = 0; $i < count($data); $i++) {
      $lineasFecha = $this->dividirTexto($data[$i]['fecha'], 19);
      $lineasEvento = $this->dividirTexto($data[$i]['evento'], 24);
      $lineasObservacion = $this->dividirTexto($data[$i]['observacion'], 42);
      $cantidadLineas = max(count($lineasFecha), count($lineasEvento), count($lineasObservacion));
      $altoFila = max(28, 14 + ($cantidadLineas * $altoLinea));

      if (($filaY - $altoFila) < 55) {
        $this->dibujarPie($pdf);
        $pdf->ezNewPage();
        $this->dibujarEncabezadoHistorial($pdf, $id);
        $this->dibujarEncabezadoTablaHistorial($pdf, $x, $y, $anchoFecha, $anchoEvento, $anchoObservacion);
        $filaY = $y - $altoEncabezado;
      }

      $this->colorHex($pdf, $i % 2 === 0 ? '#ffffff' : '#eef4fb');
      $pdf->filledRectangle($x, $filaY - $altoFila, $anchoTotal, $altoFila);
      $this->colorHex($pdf, '#d8e1ec', true);
      $pdf->rectangle($x, $filaY - $altoFila, $anchoTotal, $altoFila);
      $pdf->line($x + $anchoFecha, $filaY - $altoFila, $x + $anchoFecha, $filaY);
      $pdf->line($x + $anchoFecha + $anchoEvento, $filaY - $altoFila, $x + $anchoFecha + $anchoEvento, $filaY);

      for ($j = 0; $j < count($lineasFecha); $j++) {
        $this->texto($pdf, $x + 8, $filaY - 17 - ($j * $altoLinea), 9, $lineasFecha[$j], '#111827');
      }
      for ($j = 0; $j < count($lineasEvento); $j++) {
        $this->texto($pdf, $x + $anchoFecha + 8, $filaY - 17 - ($j * $altoLinea), 9, $lineasEvento[$j], '#111827');
      }
      for ($j = 0; $j < count($lineasObservacion); $j++) {
        $this->texto($pdf, $x + $anchoFecha + $anchoEvento + 8, $filaY - 17 - ($j * $altoLinea), 9, $lineasObservacion[$j], '#111827');
      }

      $filaY -= $altoFila;
    }
  }

  public function procesar($servicio, $datos)
  {
    $pdf = new CezPDF('a4');
    if (strpos(PHP_OS, 'WIN') !== false) {
      $pdf->tempPath = 'C:/temp';
    }

    $pdf->selectFont('Helvetica');

    $id = $servicio[0]['id_servicio'];
    $appRoot = realpath(__DIR__ . '/../../');
    $publicAppRoot = realpath(__DIR__ . '/../../../');
    $servicioDir = $publicAppRoot . DIRECTORY_SEPARATOR . 'servicios' . DIRECTORY_SEPARATOR . $id;
    $servicioDirLegacy = $appRoot . DIRECTORY_SEPARATOR . 'servicios' . DIRECTORY_SEPARATOR . $id;
    $directoriosServicio = array($servicioDir, $servicioDirLegacy);
    if (!is_dir($servicioDir) && is_dir($servicioDirLegacy)) {
      $servicioDir = $servicioDirLegacy;
    }
    $banner = $appRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'banner.jpg';
    $fechaGeneracion = date("Y-m-d H:i:s");

    $this->colorHex($pdf, '#eef4f9');
    $pdf->filledRectangle(0, 0, 595, 842);
    $this->colorHex($pdf, '#ffffff');
    $pdf->filledRectangle(32, 32, 531, 778);

    $this->addImagenCubierta($pdf, $banner, 45, 716, 505, 68);

    $this->texto($pdf, 45, 680, 19, "<b>INFORME DE SERVICIO GPS</b>", '#0f172a');
    $this->texto($pdf, 45, 662, 9, "Servicio ID: ".$id."  |  Generado: ".$fechaGeneracion, '#64748b');
    $this->colorHex($pdf, '#0f3558');
    $pdf->filledRectangle(45, 645, 505, 2);

    $this->tituloSeccion($pdf, 45, 618, "Resumen del cliente");
    $this->tablaDetalle(
      $pdf,
      array(
        array('campo' => 'Cliente', 'valor' => $this->valor($servicio[0]['razon_social']), 'campo2' => 'Contacto', 'valor2' => $this->valor($servicio[0]['nombre_contacto'])),
        array('campo' => 'Emergencia', 'valor' => $this->valor($servicio[0]['celular1']), 'campo2' => 'Ruta', 'valor2' => $this->valor($servicio[0]['ruta']))
      ),
      598
    );

    $this->tituloSeccion($pdf, 45, 532, "Datos del conductor y servicio");
    $this->tablaDetalle(
      $pdf,
      array(
        array('campo' => 'Conductor', 'valor' => $this->valor($servicio[0]['nombre_conductor']), 'campo2' => 'Placa', 'valor2' => $this->valor($servicio[0]['placa'])),
        array('campo' => 'Cedula', 'valor' => $this->valor($servicio[0]['cedula']), 'campo2' => 'Vehiculo', 'valor2' => $this->valor($servicio[0]['marca'])." / ".$this->valor($servicio[0]['color'])),
        array('campo' => 'Celular', 'valor' => $this->valor($servicio[0]['telefono1']), 'campo2' => 'Contenedor', 'valor2' => $this->valor($servicio[0]['numero_contenedor'])),
        array('campo' => 'Manifiesto', 'valor' => $this->valor($servicio[0]['manifiesto']), 'campo2' => 'Estado', 'valor2' => 'ACTIVO')
      ),
      512
    );

    $imagenSatelital = !empty($datos) ? $this->resolverImagenMinutaDisponible($directoriosServicio, $datos) : "";
    if ($imagenSatelital !== "" && is_file($imagenSatelital)) {
      $this->tituloSeccion($pdf, 45, 405, "Ultima novedad satelital");
      $this->addImagenCaja($pdf, $imagenSatelital, 45, 190, 505, 195, 'cover');
    }
    $this->dibujarPie($pdf);

    $pdf->ezNewPage();
    $this->colorHex($pdf, '#eef4f9');
    $pdf->filledRectangle(0, 0, 595, 842);
    $this->colorHex($pdf, '#ffffff');
    $pdf->filledRectangle(32, 32, 531, 778);
    $this->tituloSeccion($pdf, 45, 690, "Registro fotografico de instalacion");
    $this->addImagenCaja($pdf, $this->resolverImagen($directoriosServicio, 'carro1'), 72, 170, 210, 485, 'cover');
    $this->addImagenCaja($pdf, $this->resolverImagen($directoriosServicio, 'carro2'), 312, 170, 210, 485, 'cover');
    $this->dibujarPie($pdf);

    $pdf->ezNewPage();
    $this->dibujarEncabezadoHistorial($pdf, $id);
    $pdf->ezSetY(770);

    $data = array();
    for ($i = 0; $i < count($datos); $i++) {
      $data[] = array(
        'fecha' => $datos[$i]['fecha_novedad'],
        'evento' => $datos[$i]['evento'],
        'observacion' => $datos[$i]['observacion']
      );
    }

    if (!empty($data)) {
      $this->dibujarHistorialEventos($pdf, $data, $id);
    } else {
      $this->texto($pdf, 190, 700, 12, "No hay minutas registradas para este servicio.", '#475569');
    }

    $this->dibujarPie($pdf);

    $out = $pdf->ezOutput();
    if (!is_dir($servicioDir)) {
      @mkdir($servicioDir, 0775, true);
    }

    $ruta = $servicioDir . DIRECTORY_SEPARATOR . 'informe.pdf';
    $flujo = fopen($ruta, 'wb');
    if ($flujo) {
      fwrite($flujo, $out);
      fclose($flujo);
    }
  }
}

?>
