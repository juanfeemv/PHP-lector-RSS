<?php

// Nota: $link ahora contendrá el objeto PDO
$link = false; 

// OBTENER CREDENCIALES DE VARIABLES DE ENTORNO
// ¡Asegúrate de que POSTGRES_URL esté configurada en Vercel!
$dbUrl = getenv('POSTGRES_URL'); 

if (!empty($dbUrl)) {
    
    try {
        // Parseamos la URL de Vercel
        $url = parse_url($dbUrl);
        
        // El DSN (Data Source Name) para PostgreSQL
        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $url['host'],
            $url['port'],
            ltrim($url['path'], '/'), // Nombre de la BD
            $url['user'],
            $url['pass']
        );

        // Creamos la conexión PDO
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Configuramos la codificación
        $pdo->exec("SET NAMES 'utf8'");

        $link = $pdo; 
        
    } catch (PDOException $e) {
        $link = false;
        // Si la conexión falla, $link es FALSE, y se mostrará el error en index.php
    }
}
?>