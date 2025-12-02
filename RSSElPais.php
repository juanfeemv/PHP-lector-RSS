<?php

// Mostrar errores para depuración (puedes quitar esto en producción si prefieres)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "conexionRSS.php";

// Descargar el RSS
$sXML = download("https://ep00.epimg.net/rss/elpais/portada.xml"); 

$oXML = null;
if (!empty($sXML)) {
    try {
        $oXML = new SimpleXMLElement($sXML); 
    } catch (Exception $e) {
        // XML inválido
    }
}

require_once "conexionBBDD.php";

// Solo procedemos si hay conexión (PDO) y hay datos XML
if ($link instanceof PDO && $oXML !== null) {
            
    $categoria=["Política","Deportes","Ciencia","España","Economía","Música","Cine","Europa","Justicia"];
    
    // PREPARAR CONSULTAS (Optimización)
    // Nota las comillas en \"fPubli\", obligatorias en Postgres si la columna se creó con mayúsculas
    $sql_check = "SELECT link FROM elpais WHERE link = :link";
    $stmt_check = $link->prepare($sql_check);

    $sql_insert = "INSERT INTO elpais (titulo, link, descripcion, categoria, \"fPubli\", contenido) VALUES(:titulo, :link, :descripcion, :categoria, :fPubli, :contenido)";
    $stmt_insert = $link->prepare($sql_insert);
    
    foreach ($oXML->channel->item as $item){
        
        $Repit = false; 
        $categoriaFiltro = "";
        
        // Lógica de categorías
        for ($i=0; $i < count($item->category); $i++){ 
            for($j=0; $j < count($categoria); $j++){
                if($item->category[$i] == $categoria[$j]){
                    $categoriaFiltro = "[".$categoria[$j]."]" . $categoriaFiltro;
                }
            } 
        }

        // Formateo de fecha y contenido
        $fPubli = strtotime($item->pubDate);
        $new_fPubli = date('Y-m-d', $fPubli);
        
        // El País usa namespaces para el contenido, a veces falla si no se accede bien
        $content = $item->children("content", true);
        $encoded = (string)$content->encoded; 
        // Si encoded está vacío, usamos la descripción
        if(empty($encoded)) {
            $encoded = (string)$item->description;
        }

        // 1. VERIFICAR DUPLICADOS
        try {
            $stmt_check->execute([':link' => (string)$item->link]);
            // Si fetch devuelve algo, es que ya existe
            if ($stmt_check->fetch()) {
                $Repit = true;
            }
        } catch (PDOException $e) {
            // Error en lectura (ignorar)
        }

        // 2. INSERTAR SI ES NUEVO
        if ($Repit == false && $categoriaFiltro != "") {
            try {
                $stmt_insert->execute([
                    ':titulo' => (string)$item->title,
                    ':link' => (string)$item->link,
                    ':descripcion' => (string)$item->description,
                    ':categoria' => $categoriaFiltro,
                    ':fPubli' => $new_fPubli,
                    ':contenido' => $encoded
                ]);
            } catch (PDOException $e) {
                // Si falla el insert (ej: clave duplicada que se nos pasó), lo ignoramos para seguir
                // echo "Error Insert: " . $e->getMessage(); 
            }
        } 
    }
            
} else if ($link === false) {
    // Solo mostramos error si la conexión falló totalmente
    printf("Conexión a el periódico El País ha fallado.");
}
?>