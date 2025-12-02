<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "conexionRSS.php";

$sXML = download("https://e00-elmundo.uecdn.es/elmundo/rss/espana.xml");

$oXML = null;
if (!empty($sXML)) {
    try {
        $oXML = new SimpleXMLElement($sXML);
    } catch (Exception $e) {
    }
}

require_once "conexionBBDD.php";

if ($link instanceof PDO && $oXML !== null) {

    $categoria=["Política","Deportes","Ciencia","España","Economía","Música","Cine","Europa","Justicia"];

    $sql_check = "SELECT link FROM elmundo WHERE link = :link";
    $stmt_check = $link->prepare($sql_check);

    $sql_insert = "INSERT INTO elmundo (titulo, link, descripcion, categoria, \"fPubli\", contenido) VALUES(:titulo, :link, :descripcion, :categoria, :fPubli, :contenido)";
    $stmt_insert = $link->prepare($sql_insert);

    foreach ($oXML->channel->item as $item){ 

        $Repit = false;
        $categoriaFiltro = "";

        $media = $item->children("media", true);
        $description = (string)$item->description; 
        if(empty($description) && !empty($media->description)){
             $description = (string)$media->description;
        }

        for ($i=0; $i < count($item->category); $i++){ 
            for($j=0; $j < count($categoria); $j++){
                if($item->category[$i] == $categoria[$j]){
                    $categoriaFiltro = "[".$categoria[$j]."]" . $categoriaFiltro;
                }
            }
        }

        $fPubli = strtotime($item->pubDate);
        $new_fPubli = date('Y-m-d', $fPubli);

        $contenido = (string)$item->guid;

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
                    ':descripcion' => $description,
                    ':categoria' => $categoriaFiltro,
                    ':fPubli' => $new_fPubli,
                    ':contenido' => $contenido
                ]);
            } catch (PDOException $e) {
            }
        } 
    }

} else if ($link === false) {
    printf("Conexión a el periódico El Mundo ha fallado.");
}
?>