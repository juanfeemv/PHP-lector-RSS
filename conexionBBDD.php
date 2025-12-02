<?php

$link = false; 

// 1. Usamos la variable que Vercel te ha creado automáticamente
$dbUrl = getenv('DB_POSTGRES_URL'); 

if (!empty($dbUrl)) {
    
    try {
        // Parseamos la URL de conexión
        $url = parse_url($dbUrl);
        
        // Extraemos los datos con valores por defecto para evitar errores
        $host = $url['host'] ?? '';
        $port = $url['port'] ?? 5432;
        $user = $url['user'] ?? '';
        $pass = $url['pass'] ?? '';
        $path = ltrim($url['path'] ?? '/neondb', '/'); // Neon suele usar 'neondb' por defecto

        // 2. Construimos el DSN para PDO
        // IMPORTANTE: Añadimos 'sslmode=require' porque Neon lo exige
        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s;sslmode=require",
            $host,
            $port,
            $path,
            $user,
            $pass
        );

        // 3. Crear la conexión PDO
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Configuramos la codificación
        $pdo->exec("SET NAMES 'utf8'");

        $link = $pdo; 
        
    } catch (PDOException $e) {
        $link = false;
        // Si falla, el error se mostrará en index.php como "Conexión fallida"
    }
}
?>