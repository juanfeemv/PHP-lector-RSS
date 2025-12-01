<?php

$Repit=false;

// OBTENER CREDENCIALES DE VARIABLES DE ENTORNO
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$port = 4000; // Puerto estándar para TiDB Serverless

// Inicializamos $link a false por defecto.
$link = false; 

// ----------------------------------------------------------------------
// CORRECCIÓN CRÍTICA: Implementación de conexión SSL/TLS
// ----------------------------------------------------------------------

if (!empty($host)) {
    
    // 1. Crear un nuevo objeto mysqli
    $link = @mysqli_init();

    // 2. Establecer el modo SSL requerido (IMPORTANTE para TiDB/PlanetScale)
    // Usamos NULL para el certificado CA, confiando en la negociación del driver.
@mysqli_ssl_set($link, NULL, NULL, NULL, NULL, NULL);
    // 3. Intentar la conexión real usando el puerto y el objeto.
    // Usamos @ para suprimir errores de red/credenciales.
    $connected = @mysqli_real_connect($link, $host, $user, $password, $dbname, $port);

    if ($connected) {
        // Si la conexión tiene éxito, configuramos la codificación.
        @$tildes = $link->query("SET NAMES 'utf8'");
    } else {
        // Si falló la conexión externa.
        $link = false;
    }
} else {
    // Si no hay host configurado
    $link = false;
}

// Si $link es false, el código en RSSElPais/Mundo y index.php lo detectará y omitirá las consultas SQL.
?>