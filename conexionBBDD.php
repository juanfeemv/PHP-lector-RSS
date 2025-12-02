<?php

$link = false; 

$dbUrl = getenv('DB_POSTGRES_URL'); 

if (!empty($dbUrl)) {
    
    try {
        $url = parse_url($dbUrl);
        
        $host = $url['host'] ?? '';
        $port = $url['port'] ?? 5432;
        $user = $url['user'] ?? '';
        $pass = $url['pass'] ?? '';
        $path = ltrim($url['path'] ?? '/neondb', '/');

        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s;sslmode=require",
            $host,
            $port,
            $path,
            $user,
            $pass
        );

        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("SET NAMES 'utf8'");

        $link = $pdo; 
        
    } catch (PDOException $e) {
        $link = false;

    }
}
?>