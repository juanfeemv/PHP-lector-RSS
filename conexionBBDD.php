<?php

$link = false; 

// 1. OBTENER LA NUEVA VARIABLE DE ENTORNO: DB_URL
$dbUrl = getenv('DB_URL'); 

if (!empty($dbUrl)) {
    
    try {
        // Parseamos la URL de Supabase (PostgreSQL)
        $url = parse_url($dbUrl);
        
        // CORRECCIÓN: Asignamos valores predeterminados para evitar 'Undefined array key'
        $host = $url['host'] ?? '';
        $port = $url['port'] ?? 5432; // Usamos 5432 como fallback (o 6543 si usas pooler)
        $user = $url['user'] ?? '';
        $pass = $url['pass'] ?? '';
        $path = $url['path'] ?? '/postgres';
        
        // Construimos el DSN
        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $host,
            $port,
            ltrim($path, '/'), // Nombre de la BD
            $user,
            $pass
        );

        // Creamos la conexión PDO
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("SET NAMES 'utf8'");

        $link = $pdo; 
        
    } catch (PDOException $e) {
        $link = false;
        // Si la conexión falla, $link es FALSE
    }
}
?>