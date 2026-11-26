<?php 


include_once("/home/segcontr/public_html/app/segcontrol/lib/phpMailer/class.phpmailer.php");
include_once("/home/segcontr/public_html/app/segcontrol/lib/phpMailer/class.smtp.php");

include_once("/home/segcontr/public_html/app/segcontrol/model/cliente.php");

class  mail{

public function correo(cliente $modelCliente,$de,$para,$asuntoh,$mensajeh,$correo,$clave,$idservicio){

  $cabeceras = "MIME-Version: 1.0\r\n";
  $cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
  $cabeceras .= "From: $de \r\n";
  $publicAppRoot = realpath(__DIR__ . '/../../../');
  $destino = $publicAppRoot . DIRECTORY_SEPARATOR . 'servicios' . DIRECTORY_SEPARATOR . intval($idservicio) . DIRECTORY_SEPARATOR . 'informe.pdf';
  $mail = new PHPMailer();

  if (!is_file($destino)) {
    return array(
      'ok' => false,
      'mensaje' => 'No se encontro el PDF del servicio para adjuntar al correo.',
      'detalle' => $destino
    );
  }

  if($mail->AddAttachment($destino)){
  	//incluyo la clase phpmailer
  	//$mail = new PHPMailer(); //creo un objeto de tipo PHPMailer
  	$mail->IsSMTP(); //protocolo SMTP
  	$mail->SMTPAuth = true;//autenticaciÃƒÂ¯Ã‚Â¿Ã‚Â½n en el SMTP
  	$mail->SMTPSecure = "tls";//SSL security socket layer
  	$mail->Host = "smtp.gmail.com";//servidor de SMTP de gmail
  	$mail->Port = 587;//puerto seguro del servidor SMTP de gmail
  	$mail->Username = (string) getenv('SEGCONTROL_SMTP_USER');//Aqui pon tu correo de gmail
  	$mail->Password = (string) getenv('SEGCONTROL_SMTP_PASSWORD');//Aqui pon tu contrasena de gmail
  	$mail->setFrom($mail->Username, 'SEGCONTROL GPS'); //Remitente del correo
    $destinatarios=array($modelCliente->getEmail(),
    $modelCliente->getEmail2(),
    $modelCliente->getEmail3(),
    $modelCliente->getEmail4(),
    $modelCliente->getEmail5(),
    $modelCliente->getEmail6(),
    $modelCliente->getEmail7(),
    $modelCliente->getEmail8());
    $tieneDestinatario = false;
    foreach ($destinatarios as $email) {
        $email = trim((string) $email);
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mail->AddAddress($email);
            $tieneDestinatario = true;
        }
    }
    $para = trim((string) $para);
    if ($para !== '' && filter_var($para, FILTER_VALIDATE_EMAIL)) {
        $mail->AddAddress($para);
        $tieneDestinatario = true;
    }
    if (!$tieneDestinatario) {
      return array(
        'ok' => false,
        'mensaje' => 'No se encontro un correo destino valido para enviar la notificacion.',
        'detalle' => 'DESTINATARIO_INVALIDO'
      );
    }
  	$mail->Subject = $asuntoh; //Asunto del correo
  	$mail->Body = $mensajeh; //Contenido del correo
  	$mail->WordWrap = 50; //No. de columnas
  	$mail->MsgHTML($mensajeh);//Se indica que el cuerpo del correo tendrÃƒÂ¯Ã‚Â¿Ã‚Â½ formato html
  	//$mail->AddAttachment($destino); //accedemos al archivo que se subio al servidor y lo adjuntamos
  	if($mail->Send()){ //enviamos el correo por PHPMailer
  		return array(
        'ok' => true,
        'mensaje' => 'Correo enviado correctamente.',
        'detalle' => ''
      );
  	} else{
  		return array(
        'ok' => false,
        'mensaje' => 'La minuta fue creada, pero el correo no pudo enviarse.',
        'detalle' => $mail->ErrorInfo
      );
  	}
  } else {
   	return array(
      'ok' => false,
      'mensaje' => 'No fue posible adjuntar el PDF al correo.',
      'detalle' => $destino
    );
  }

}

}

 ?>
