<?php
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
?>
<style>
  .service-menu-list {
    margin-top: 10px;
  }

  .service-menu-item {
    align-items: center;
    border-bottom: 1px solid #eef2f7;
    display: flex;
    gap: 18px;
    min-height: 86px;
    padding: 14px 4px;
  }

  .service-menu-item:last-child {
    border-bottom: 0;
  }

  .service-menu-icon {
    align-items: center;
    border-radius: 8px;
    color: #fff;
    display: inline-flex;
    flex: 0 0 58px;
    height: 58px;
    justify-content: center;
    text-decoration: none;
    transition: box-shadow .2s ease, transform .2s ease;
    width: 58px;
  }

  .service-menu-icon:hover,
  .service-menu-icon:focus {
    box-shadow: 0 8px 18px rgba(0, 0, 0, .18);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .service-menu-icon .material-icons {
    font-size: 30px;
  }

  .service-menu-primary { background: #1f91f3; }
  .service-menu-success { background: #2eaf62; }
  .service-menu-info { background: #00acc1; }
  .service-menu-warning { background: #ff9800; }
  .service-menu-danger { background: #f5365c; }

  .service-menu-text {
    color: #5f6f86;
    font-size: 17px;
    line-height: 1.45;
  }
</style>

<div class="row clearfix">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
      <div class="header">
        <div class="font-20">
          <h3>Gestion de registro de servicios y minutas</h3>
        </div>
      </div>
      <div class="body">
        <div class="service-menu-list">
          <div class="service-menu-item">
            <a href="#" id="nuevoServicio" class="service-menu-icon service-menu-primary" title="Crear servicio">
              <i class="material-icons">add_circle_outline</i>
            </a>
            <div class="service-menu-text">Modulo para crear nuevos servicios en el sistema.</div>
          </div>

          <div class="service-menu-item">
            <a href="#" id="listaServicios" class="service-menu-icon service-menu-success" title="Servicios activos">
              <i class="material-icons">playlist_add_check</i>
            </a>
            <div class="service-menu-text">Modulo para cargar todos servicios activos.</div>
          </div>

          <div class="service-menu-item">
            <a href="#" id="historialServicio" class="service-menu-icon service-menu-info" title="Historial de servicios">
              <i class="material-icons">history</i>
            </a>
            <div class="service-menu-text">Modulo para cargar un historial de servicios almacenados en el sistema.</div>
          </div>

          <div class="service-menu-item">
            <a href="#" id="aseguradoraMain" class="service-menu-icon service-menu-warning" title="Aseguradoras">
              <i class="material-icons">verified_user</i>
            </a>
            <div class="service-menu-text">Modulo para crear nuevos registros de aseguradoras en el sistema.</div>
          </div>

          <div class="service-menu-item">
            <a href="#" id="acompananteMain" class="service-menu-icon service-menu-danger" title="Acompanantes">
              <i class="material-icons">supervisor_account</i>
            </a>
            <div class="service-menu-text">Modulo para crear nuevos acompanantes en el sistema.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
