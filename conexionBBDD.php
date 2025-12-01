<?php

$Repit=false;
// Estas credenciales NO funcionarán en Vercel, causarán un error de conexión.
$host="localhost";
$user="root";
$password="";

// Usamos @ para suprimir errores fatales de conexión a BD local en Vercel.
// La BD externa es obligatoria para la funcionalidad completa.
$link= @mysqli_connect($host,$user,$password);

if ($link) {
    // Si la conexión (sorprendentemente) tiene éxito, intentamos configurar.
    @$tildes = $link->query("SET NAMES 'utf8'");
    @mysqli_select_db($link,'periodicos');
}

?>