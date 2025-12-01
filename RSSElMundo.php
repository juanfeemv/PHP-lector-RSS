<?php

require_once "conexionRSS.php";

$sXML=download("https://e00-elmundo.uecdn.es/elmundo/rss/espana.xml");

$oXML=new SimpleXMLElement($sXML);

require_once "conexionBBDD.php";

// ----------------------------------------------------------------------
// CORRECCIÓN CRÍTICA: Solo se intenta hacer consultas a la BD si $link es válido
// ----------------------------------------------------------------------
if ($link !== false) {

    if(mysqli_connect_error()){
        printf("Conexión a el periódico El Mundo ha fallado");
    }else{

        $contador=0;
        
        $categoria=["Política","Deportes","Ciencia","España","Economía","Música","Cine","Europa","Justicia"];
        $categoriaFiltro="";

        foreach ($oXML->channel->item as $item){ //es un for a la que le hemos dicho que extraer y donde almacenarlo

            
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

            $sql="SELECT link FROM elmundo";
            $result= mysqli_query($link,$sql);
            
            while($sqlCompara=mysqli_fetch_array($result)){
                        
                        
                if($sqlCompara['link']==$item->link){
                        
                    $Repit=true;
                    $contador=$contador+1;
                    $contadorTotal=$contador;
                    break;
                    } else {
                    $Repit=false;
                }
                        
                        
            }
                if($Repit==false && $categoriaFiltro<>""){
                    
                    $sql="INSERT INTO elmundo VALUES('','$item->title','$item->link','$description','$categoriaFiltro','$new_fPubli','$item->guid')";
                    $result= mysqli_query($link, $sql);
                    
                    
                } 
                $categoriaFiltro="";

        }
    }
}
?>