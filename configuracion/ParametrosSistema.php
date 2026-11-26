<?php

class ParametrosSistema
{
    function getNombreBD()
    {
        return (string) getenv('SEGCONTROL_DB_NAME');
    }

    function getIPBD()
    {
        $host = getenv('SEGCONTROL_DB_HOST');
        return $host === false || $host === '' ? 'localhost' : $host;
    }

    function getUsuarioBD()
    {
        return (string) getenv('SEGCONTROL_DB_USER');
    }

    function getClaveBD()
    {
        return (string) getenv('SEGCONTROL_DB_PASSWORD');
    }
}
