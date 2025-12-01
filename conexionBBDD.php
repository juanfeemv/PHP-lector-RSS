<?php

$Repit=false;

// OBTENER CREDENCIALES DE VARIABLES DE ENTORNO
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Inicializamos $link a false por defecto.
$link = false; 

// ----------------------------------------------------------------------
// CORRECCIÓN CRÍTICA: Solo intentamos la conexión si DB_HOST está configurado
// Si $host está vacío (Variables de Entorno no configuradas en Vercel),
// omitimos la conexión para evitar el error "No such file or directory".
// ----------------------------------------------------------------------

if (!empty($host)) {
    // Si $host está configurado, intentamos la conexión externa.
    // Usamos @ para suprimir errores de red/credenciales.
    $link = @mysqli_connect($host, $user, $password, $dbname);

    if ($link) {
        // Si la conexión tiene éxito, configuramos la codificación.
        @$tildes = $link->query("SET NAMES 'utf8'");
    } else {
        // Si falló la conexión externa, pero el host estaba definido.
        $link = false;
    }
}

// Si $link es false, el código en RSSElPais/Mundo lo detectará y omitirá las consultas SQL.
?>