<style>
  .resource-menu-list {
    margin-top: 8px;
  }

  .resource-menu-title {
    margin: 0 0 14px;
    color: #263238;
    font-size: 21px;
    font-weight: 500;
    letter-spacing: 0;
    text-transform: uppercase;
  }

  .resource-menu-item {
    align-items: center;
    border-bottom: 1px solid #e8eef6;
    display: flex;
    gap: 18px;
    min-height: 92px;
    padding: 16px 0;
  }

  .resource-menu-item:last-child {
    border-bottom: 0;
  }

  .resource-menu-icon {
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

  .resource-menu-icon:hover,
  .resource-menu-icon:focus {
    box-shadow: 0 8px 18px rgba(0, 0, 0, .18);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .resource-menu-icon i {
    font-size: 31px;
  }

  .resource-menu-driver { background: #1f91f3; }
  .resource-menu-client { background: #00acc1; }
  .resource-menu-vehicle { background: #2eaf62; }

  .resource-menu-content h4 {
    margin: 0 0 6px;
    color: #536274;
    font-size: 16px;
    font-weight: 500;
  }

  .resource-menu-content p {
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
          <h3 class="resource-menu-title">Modulo de gestion de recursos e infraestructura</h3>

          <div class="resource-menu-list">
            <div class="resource-menu-item">
              <a href="#" id="conductorMain" class="resource-menu-icon resource-menu-driver" title="Conductores">
                <i class="material-icons">person_pin</i>
              </a>
              <div class="resource-menu-content">
                <h4>Modulo de conductores</h4>
                <p>Crear, consultar o desactivar registros de conductores en el sistema.</p>
              </div>
            </div>

            <div class="resource-menu-item">
              <a href="#" id="clienteMain" class="resource-menu-icon resource-menu-client" title="Clientes">
                <i class="material-icons">business</i>
              </a>
              <div class="resource-menu-content">
                <h4>Modulo de clientes</h4>
                <p>Crear, consultar o desactivar clientes.</p>
              </div>
            </div>

            <div class="resource-menu-item">
              <a href="#" id="vehiculoMain" class="resource-menu-icon resource-menu-vehicle" title="Vehiculos">
                <i class="material-icons">local_shipping</i>
              </a>
              <div class="resource-menu-content">
                <h4>Modulo de vehiculos</h4>
                <p>Crear, consultar o desactivar los registros de los vehiculos en el sistema.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
