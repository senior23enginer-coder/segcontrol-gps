<?php header("access-control-allow-origin: *"); 

    require_once("/home/segcontr/public_html/app/segcontrol/controller/correoServiciosController.php");
    require_once("/home/segcontr/public_html/app/segcontrol/model/correoServicios.php");

    $controllerCorreo=new correoServiciosController();

    $act=0;
    $controllerCorreo->actualizarCorreoServiciosActivacion($act);
    $modelCorreo = new correoServicios(
        "NULL",
        $_POST["correo"],
        $_POST["clave"],
        1
    );
    if($controllerCorreo->registrarCorreoServicios($modelCorreo)>0){
            echo "<div class='alert alert-success'><strong>Correo electronico registrado correctamente.</strong></div>";
        }else{
            echo "<div class='alert alert-warning'><strong>El correo electronico no ha sido registrado correctamente.</strong></div>";
        }
?>
