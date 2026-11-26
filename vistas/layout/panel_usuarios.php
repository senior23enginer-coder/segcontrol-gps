<?php
require_once("/home/segcontr/public_html/app/segcontrol/controller/conductorController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/vehiculoController.php");
require_once("/home/segcontr/public_html/app/segcontrol/controller/serviciosController.php");
?>
<style>
  .user-admin-menu-list {
    margin-top: 8px;
  }

  .user-admin-menu-title {
    margin: 0 0 14px;
    color: #263238;
    font-size: 21px;
    font-weight: 500;
    letter-spacing: 0;
    text-transform: uppercase;
  }

  .user-admin-menu-item {
    align-items: center;
    border-bottom: 1px solid #e8eef6;
    display: flex;
    gap: 18px;
    min-height: 92px;
    padding: 16px 0;
  }

  .user-admin-menu-item:last-child {
    border-bottom: 0;
  }

  .user-admin-menu-icon {
    align-items: center;
    border-radius: 8px;
    color: #fff;
    display: inline-flex;
    flex: 0 0 64px;
    height: 64px;
    justify-content: center;
    text-decoration: none;
    transition: box-shadow .2s ease, transform .2s ease;
    width: 64px;
  }

  .user-admin-menu-icon:hover,
  .user-admin-menu-icon:focus {
    box-shadow: 0 8px 18px rgba(0, 0, 0, .18);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .user-admin-menu-icon i {
    font-size: 32px;
  }

  .user-admin-menu-employee { background: #1f91f3; }
  .user-admin-menu-client { background: #00acc1; }

  .user-admin-menu-content h4 {
    margin: 0 0 6px;
    color: #536274;
    font-size: 16px;
    font-weight: 500;
  }

  .user-admin-menu-content p {
    margin: 0;
    color: #65758b;
    font-size: 16px;
    line-height: 1.45;
  }
</style>

<div id="contendor_dinamico_main">
  <div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="body">
          <h3 class="user-admin-menu-title">Modulo de administracion de usuarios</h3>

          <div class="user-admin-menu-list">
            <div class="user-admin-menu-item">
              <a href="#" id="empleadoMain" class="user-admin-menu-icon user-admin-menu-employee" title="Empleados">
                <i class="material-icons">assignment_ind</i>
              </a>
              <div class="user-admin-menu-content">
                <h4>Empleados</h4>
                <p>Crear, modificar o desactivar un empleado del sistema.</p>
              </div>
            </div>

            <div class="user-admin-menu-item">
              <a href="#" id="clienteMain" class="user-admin-menu-icon user-admin-menu-client" title="Clientes">
                <i class="material-icons">business</i>
              </a>
              <div class="user-admin-menu-content">
                <h4>Clientes</h4>
                <p>Crear, modificar o desactivar un cliente del sistema.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
