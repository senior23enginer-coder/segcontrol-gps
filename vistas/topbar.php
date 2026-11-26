<?php
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$vistasPos = strpos($scriptName, '/vistas/');
$vistasBaseUrl = $vistasPos !== false
    ? substr($scriptName, 0, $vistasPos + strlen('/vistas/'))
    : '';
$loginUrl = $vistasBaseUrl . 'index.php';
$logoutUrl = $vistasBaseUrl . 'logout.php';
?>
<div class="overlay"></div>
  <nav class="navbar">
      <div class="container-fluid">
          <div class="navbar-header">
              <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
              <a href="javascript:void(0);" class="bars"></a>
              <a class="navbar-brand" href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>">SEGCONTROL GPS</a>
              <button type="button" id="toggleSidebar" class="sidebar-toggle-button" title="Mostrar u ocultar menu" aria-label="Mostrar u ocultar menu" aria-expanded="true">
                  <i class="material-icons">menu_open</i>
              </button>
          </div>
          <div class="collapse navbar-collapse" id="navbar-collapse">
              <ul class="nav navbar-nav navbar-right">
                  <li><a href="<?php echo htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8'); ?>" id="cerrarSesion" class="mega-menu" data-close="true"><i class="zmdi zmdi-power"></i></a></li>
              </ul>
          </div>
      </div>
  </nav>
