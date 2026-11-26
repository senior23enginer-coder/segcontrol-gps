<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>:: INTRANET  ::</title>
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link href="../assets/css/main.css" rel="stylesheet">
  <link href="../assets/css/login.css" rel="stylesheet">
  <link href="../assets/css/themes/all-themes.css" rel="stylesheet" />
  <style>
    .login-alert-overlay {
      align-items: center;
      background: rgba(17, 24, 39, 0.56);
      bottom: 0;
      display: none;
      justify-content: center;
      left: 0;
      padding: 24px;
      position: fixed;
      right: 0;
      top: 0;
      z-index: 9999;
    }

    .login-alert-overlay.is-visible {
      display: flex;
    }

    .login-alert-dialog {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 22px 70px rgba(15, 23, 42, 0.32);
      max-width: 420px;
      overflow: hidden;
      width: 100%;
    }

    .login-alert-header {
      align-items: center;
      background: #f5365c;
      color: #fff;
      display: flex;
      gap: 12px;
      padding: 18px 22px;
    }

    .login-alert-icon {
      align-items: center;
      border: 2px solid rgba(255, 255, 255, 0.78);
      border-radius: 50%;
      display: flex;
      font-size: 22px;
      font-weight: 700;
      height: 34px;
      justify-content: center;
      line-height: 1;
      width: 34px;
    }

    .login-alert-title {
      font-size: 18px;
      font-weight: 700;
      margin: 0;
    }

    .login-alert-body {
      color: #334155;
      font-size: 15px;
      line-height: 1.55;
      padding: 22px;
    }

    .login-alert-actions {
      border-top: 1px solid #eef2f7;
      padding: 14px 22px 20px;
      text-align: right;
    }

    .login-alert-button {
      background: #f5365c;
      border: 0;
      border-radius: 4px;
      color: #fff;
      cursor: pointer;
      font-weight: 700;
      min-width: 112px;
      padding: 10px 18px;
      text-transform: uppercase;
    }
  </style>
</head>

<body class="theme-cyan">
  <div class="login-alert-overlay" id="loginErrorModal" role="dialog" aria-modal="true" aria-labelledby="loginErrorTitle">
    <div class="login-alert-dialog">
      <div class="login-alert-header">
        <div class="login-alert-icon">!</div>
        <h3 class="login-alert-title" id="loginErrorTitle">No pudimos iniciar sesion</h3>
      </div>
      <div class="login-alert-body" id="loginErrorMessage">
        Revisa los datos ingresados e intenta nuevamente.
      </div>
      <div class="login-alert-actions">
        <button type="button" class="login-alert-button" id="cerrarLoginError">Entendido</button>
      </div>
    </div>
  </div>
  <div class="authentication">
    <div class="container-fluid">
      <div class="row clearfix">
        <div class="col-lg-9 col-md-8 col-xs-12">
          <div class="l-detail">
            <h5>Bienvenido</h5>
            <h1>SEGCONTROL GPS<span> INTRANET</span></h1>
            <h3>Plataforma en servicio</h3>
            <p class="justificar">en segcontrol Implementamos tecnología de alta calidad en sistema de
              posicionamiento global GPS/GPRS, minimizando así el riego de pérdida o peinados de la
              mercancía en ruta, permitiéndole a nuestros clientes contar con una trazabilidad total,
              control en tiempo real y máxima seguridad en la operación portuaria y en el transporte de
              carga en contenedores.</p>
              <p class="justificar">DESARROLLADO POR :<br><br>
                TEAM DEV - SEGCONTROL GPS - 2026 </p>
              <ul class="list-unstyled l-social">
                <li><a href="#"><i class="zmdi zmdi-facebook-box"></i></a></li>
                <li><a href="#"><i class="zmdi zmdi-twitter"></i></a></li>
                <li><a href="#"><i class="zmdi zmdi-youtube-play"></i></a></li>
                <li><a href="#"><i class="zmdi zmdi-google-plus-box"></i></a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-xs-12">
            <div class="card">
              <h4 class="l-login">Plataforma de acceso.
                <div class="msg">Version 2.0</div>
                <br/>
                <div id ="smg_login" class="justificar msg" style="display:none;"></div>
              </h4>
              <form class="col-md-12" id="" method="POST">
                <div class="form-group form-float">
                  <label class="form-label">Usuario o Correo Electronico</label>
                  <div class="form-line">
                    <input type="text" name="usuario" id="usuario" class="form-control">
                  </div>
                </div>
                <div class="form-group form-float">
                  <label class="form-label">Contraseña</label>
                  <div class="form-line">
                    <input type="password"  name="clave" id="clave"  class="form-control">
                  </div>
                </div>
                <div class="text-left"> <a href="#" id="validarSesion" class="btn btn-raised waves-effect bg-red" type="submit">SESION</a> </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="../assets/bundles/libscripts.js"></script>
    <script src="../assets/bundles/vendorscripts.bundle.js"></script>
    <script src="../assets/plugins/css-gradientify/gradientify.min.js"></script>

    <script src="../assets/bundles/mainscripts.bundle.js"></script>
    <script src="../assets/js/rutas/ruta.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $("body").gradientify({
        gradients: [
          { start: [49,76,172], stop: [242,159,191] },
          { start: [255,103,69], stop: [240,154,241] },
          { start: [33,229,241], stop: [235,236,117] }
        ]
      });
    });
    </script>
  </body>

  </html>
