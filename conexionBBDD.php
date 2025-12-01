<?php

// NOTA: Estas credenciales locales NO funcionan en Vercel. 
// La Base de Datos debe ser externa (ej: PlanetScale, ClearDB, RDS).
// Las definimos aquí para mantener la estructura, pero la conexión fallará por diseño.

$Repit=false;
$host="localhost";
$user="root";
$password="";

// ----------------------------------------------------------------------
// IMPORTANTE: Bloqueamos intencionalmente la conexión directa a localhost
// para evitar el error "No such file or directory" en el entorno Vercel.
// La variable $link se inicializa a FALSE para que index.php muestre
// el mensaje de error de conexión y NO intente ejecutar consultas SQL.
// ----------------------------------------------------------------------

// Inicializamos $link a false (conexión fallida)
$link = false; 

// Si alguna vez usas una BD EXTERNA y variables de entorno de Vercel (ej: $host=getenv('DB_HOST')),
// el código de conexión debería ir aquí, y $link podría ser exitoso.

// La función mysqli_connect_error() buscará errores en esta conexión "simuladamente fallida".
// Como $link es false, if(mysqli_connect_error()) será TRUE en index.php, mostrando tu mensaje.
?>