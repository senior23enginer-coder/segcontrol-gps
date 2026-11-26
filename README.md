# Segcontrol GPS

Codigo de la aplicacion PHP que se sirve bajo /app/segcontrol. El arbol se obtuvo
del hosting de produccion por FTPS de solo lectura el 26 de noviembre de 2026.

## Configuracion local

Define SEGCONTROL_DB_HOST, SEGCONTROL_DB_NAME, SEGCONTROL_DB_USER y
SEGCONTROL_DB_PASSWORD en el entorno PHP. Configura SEGCONTROL_SMTP_USER y
SEGCONTROL_SMTP_PASSWORD para el envio de minutas. El repositorio incluye
el SQL de indices de la aplicacion. Para ejecutar la aplicacion se necesita
una base compatible, aprovisionada por separado.

## Alcance de la copia

Se seleccionaron codigo, recursos estaticos y el SQL de indices. La copia adapta
los parametros de base de datos y SMTP a variables de entorno. Archivos de
prueba, documentos generados, datos operativos, respaldos y configuracion FTP
se administran fuera de Git.
Se omite una fotografia suelta sin referencia en el codigo. PHP 8.2 analiza
193 de 194 scripts. lib/phpMailer/PHPMailer.php conserva el fallo de sintaxis
de la copia desplegada, que declara un namespace despues de llamar a header.
El envio de minutas utiliza class.phpmailer.php y class.smtp.php.

## Guía del código fuente

- [index.php](./index.php) es el punto de entrada web.
- [vistas](./vistas) contiene la interfaz. [controller/route](./controller/route) contiene las rutas PHP de la aplicación.
- [persistencia](./persistencia) y [model](./model) contienen el acceso a datos y los modelos.
- [database_performance_indexes.sql](./database_performance_indexes.sql) contiene índices para una base compatible.

### Verificación local

Usa php -l para comprobar la sintaxis de los archivos que modifiques. Para cambios en la interfaz de rutas, ejecuta node --check assets/js/rutas/ruta.js. Las pruebas de correo y acceso a datos requieren una base y un servidor SMTP de prueba.

### Referencia de producción

La actualización más reciente observada en la revisión por FTPS fue la de vistas/layout/servicios/bitacora.php, el 23 de noviembre de 2026 a las 00:22:51, hora de Colombia. La copia de código de este repositorio se obtuvo el 26 de noviembre de 2026.
