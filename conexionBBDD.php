<?php

$Repit=false;

// OBTENER CREDENCIALES DE VARIABLES DE ENTORNO (la forma correcta en Vercel)
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Intentar la conexión. Si las variables no existen o el host es inaccesible, fallará.
// Pasamos el nombre de la BD directamente en la conexión.
$link = @mysqli_connect($host, $user, $password, $dbname);

if (!$link) {
    // Si la conexión falla (lo esperado sin BD externa), forzamos $link a FALSE
    // Esto es crucial para que los scripts RSSElPais/Mundo puedan verificarlo.
    $link = false; 
} else {
    // Si la conexión tiene éxito (solo con BD externa configurada)
    @$tildes = $link->query("SET NAMES 'utf8'");
}

?>