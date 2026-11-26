<?php header("access-control-allow-origin: *"); 


    require_once("/home/segcontr/public_html/app/segcontrol/controller/correoServiciosController.php");
    require_once("/home/segcontr/public_html/app/segcontrol/model/correoServicios.php");

    $controllerCorreo=new correoServiciosController();
    
    $act=0;
    $id=$_POST["idCorreo"];
    $activacion=1;
    
    $controllerCorreo->actualizarCorreoServiciosActivacion($act);

    if($controllerCorreo->actualizarCorreoServicios($id,$activacion)>0){
        echo "<div class='alert alert-success'><strong>Correo electronico asignado correctamente.</strong></div>";
    }else{
        echo "<div class='alert alert-warning'><strong>No se pudo asignar el correo electronico seleccionado.</strong></div>";
    }   
    
?>
