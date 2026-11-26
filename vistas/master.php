<?php 
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once("/home/segcontr/public_html/app/segcontrol/controller/usuariosController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/clienteController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/empleadoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
require_once(__DIR__."/helpers/authViewHelper.php");
require_once(__DIR__."/helpers/sesionViewHelper.php");

sc_requerir_sesion_autenticada();
$sesion_id = sc_sesion_usuario_id();
$rol = sc_sesion_rol_id();

if (!empty($_SERVER['QUERY_STRING']) && !headers_sent()) {
  header('Location: master.php');
  exit;
}

$contextoSesion = sc_crear_contexto_sesion($sesion_id, $rol);
sc_aplicar_contexto_sesion($contextoSesion);

$sesion_rol = $contextoSesion['rol'];
$sesion_foto = $contextoSesion['foto'];
$sesion_nombre = $contextoSesion['nombre'];
$id = $contextoSesion['id'];
?>
<!DOCTYPE html>
<html>
<?php   require_once("head.php");?>
<body class="theme-blue">
  <input id="rolHidden" name="rolHidden" type="hidden" value="<?php   echo $sesion_rol; ?>" />
  <input id="idempleadoHiddenSesion" name="idempleadoHiddenSesion" type="hidden" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" />
  <?php   
  require_once("topbar.php"); 
  require_once("carga.php");?>
  <section>
    <?php 
    if(strcmp($sesion_rol,"administrador")==0){
      require_once("sidebarAdmin.php");
      require_once("sidebarConfig.php");
    }else{
      if(strcmp($sesion_rol,"cliente")==0){require_once("sidebarCliente.php");}
      else{
        require_once("sidebarAsistente.php");}
      }?>
    </section>
    <section class="content home">
 
        <span id="smg_panel">
        </span>
      
      <div class="container-fluid">
        <div class="row clearfisx">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div id="contenido">
              <?php
              if(strcmp($sesion_rol,"administrador")==0){
                require_once("/home/segcontr/public_html/app/segcontrol/vistas/administrador.php");

              }else{
                if(strcmp($sesion_rol,"cliente")==0){
                  require_once("/home/segcontr/public_html/app/segcontrol/vistas/cliente.php");
                }else{
                  require_once("/home/segcontr/public_html/app/segcontrol/vistas/asistente.php");
                }
              }
              ?>

            </div>
          </div>
        </div>

          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="card">
              <div class="body" id="footer">
                <div class="row">
                      <div class="col-xs-12">
                        <p class="copy m-b-0">© Copyright
                          <time class="year">2018</time>
                          SEGCONTROL GPS   </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </section>
            <?php   require_once("footer.php");?>
          </body>
          </html>
