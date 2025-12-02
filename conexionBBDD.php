<?php

$link = false; 

// 1. OBTENER LA NUEVA VARIABLE DE ENTORNO: SUPABASE_URL
$dbUrl = getenv('SUPABASE_URL'); 

if (!empty($dbUrl)) {
    
    try {
        // Parseamos la URL de Supabase (PostgreSQL)
        $url = parse_url($dbUrl);
        
        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $url['host'],
            $url['port'],
            ltrim($url['path'], '/'),
            $url['user'],
            $url['pass']
        );

        // Creamos la conexión PDO
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("SET NAMES 'utf8'");

        $link = $pdo; 
        
    } catch (PDOException $e) {
        $link = false;
        // Si la conexión falla, $link es FALSE, y se mostrará el error.
    }
}
?>