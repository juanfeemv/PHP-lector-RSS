<?php

require_once "conexionRSS.php";

$sXML=download("https://e00-elmundo.uecdn.es/elmundo/rss/espana.xml");

try {
    $oXML=new SimpleXMLElement($sXML);
} catch (Exception $e) {
    $oXML = null;
}

require_once "conexionBBDD.php";

// ----------------------------------------------------------------------
// VERIFICACIÓN PDO FINAL
// ----------------------------------------------------------------------
if ($link instanceof PDO && $oXML !== null) {

    $contador=0;
    
    $categoria=["Política","Deportes","Ciencia","España","Economía","Música","Cine","Europa","Justicia"];
    $categoriaFiltro="";

    foreach ($oXML->channel->item as $item){ 

        
        $media = $item->children("media", true);
        $description = $media->description; 
        

        for ( $i=0 ;$i<count($item->category); $i++){ 

            for($j=0; $j<count($categoria); $j++){

                        if($item->category[$i]==$categoria[$j]){
                            $categoriaFiltro="[".$categoria[$j]."]".$categoriaFiltro;
                        }
                    }


        }


        $fPubli= strtotime($item->pubDate);
        $new_fPubli= date('Y-m-d', $fPubli);

        $media = $item->children("media", true);
        $description = $media->description; 

        
        try {
            // PDO: Consulta SELECT para verificar si el link existe
            $sql="SELECT link FROM elmundo WHERE link = :link";
            $stmt = $link->prepare($sql);
            $stmt->execute([':link' => (string)$item->link]);
            $sqlCompara = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener resultado
        } catch (PDOException $e) {
            $sqlCompara = false;
        }
        
        // La consulta encontró el link (ya existe)
        if($sqlCompara !== false){
            $Repit=true; 
            $contador=$contador+1;
        } else {
            $Repit=false;
        }

        if($Repit==false && $categoriaFiltro<>""){
            
            try {
                // PDO: INSERT con placeholders
                $sql="INSERT INTO elmundo (titulo, link, descripcion, categoria, fPubli, contenido) VALUES(:titulo, :link, :descripcion, :categoria, :fPubli, :contenido)";
                $stmt = $link->prepare($sql);
                
                $stmt->execute([
                    ':titulo' => (string)$item->title,
                    ':link' => (string)$item->link,
                    ':descripcion' => (string)$description,
                    ':categoria' => $categoriaFiltro,
                    ':fPubli' => $new_fPubli,
                    ':contenido' => (string)$item->guid
                ]);
            } catch (PDOException $e) {
                 // print("Error INSERT: " . $e->getMessage()); // Debugging
            }
            
        } 
        $categoriaFiltro="";

    }

} else if ($link === false) {
    printf("Conexión a el periódico El Mundo ha fallado.");
}
?>