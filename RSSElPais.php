<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "conexionRSS.php";

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

if ($link instanceof PDO && $oXML !== null) {
            
    $categoria=["Política","Deportes","Ciencia","España","Economía","Música","Cine","Europa","Justicia"];
    
    $sql_check = "SELECT link FROM elpais WHERE link = :link";
    $stmt_check = $link->prepare($sql_check);

    $sql_insert = "INSERT INTO elpais (titulo, link, descripcion, categoria, \"fPubli\", contenido) VALUES(:titulo, :link, :descripcion, :categoria, :fPubli, :contenido)";
    $stmt_insert = $link->prepare($sql_insert);
    
    foreach ($oXML->channel->item as $item){
        
        $Repit = false; 
        $categoriaFiltro = "";
        
        for ($i=0; $i < count($item->category); $i++){ 
            for($j=0; $j < count($categoria); $j++){
                if($item->category[$i] == $categoria[$j]){
                    $categoriaFiltro = "[".$categoria[$j]."]" . $categoriaFiltro;
                }
            } 
        }

        $fPubli = strtotime($item->pubDate);
        $new_fPubli = date('Y-m-d', $fPubli);
        
        $content = $item->children("content", true);
        $encoded = (string)$content->encoded; 
        if(empty($encoded)) {
            $encoded = (string)$item->description;
        }

        try {
            $stmt_check->execute([':link' => (string)$item->link]);
            if ($stmt_check->fetch()) {
                $Repit = true;
            }
        } catch (PDOException $e) {
        }

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
            }
        } 
    }
            
} else if ($link === false) {
    printf("Conexión a el periódico El País ha fallado.");
}
?>