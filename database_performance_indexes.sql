-- Indices conservadores para mejorar consultas frecuentes de SEGCONTROL.
-- Ejecutar una sola vez en la base de datos del aplicativo.

ALTER TABLE servicio ADD INDEX idx_servicio_estado_fecha (estado, fecha_creacion);
ALTER TABLE servicio ADD INDEX idx_servicio_fecha_creacion (fecha_creacion);
ALTER TABLE servicio_novedad ADD INDEX idx_servicio_novedad_servicio_fecha (id_servicio, fecha_novedad);
ALTER TABLE servicio_novedad ADD INDEX idx_servicio_novedad_servicio_idnovedad (id_servicio, idservicio_novedad);
ALTER TABLE aseguradoras ADD INDEX idx_aseguradoras_cliente (id_cliente);
ALTER TABLE aseguradoras ADD INDEX idx_aseguradoras_acompanante (id_acompanante);
