<?php

function download($ruta){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$ruta);
    curl_setopt($ch,CURLOPT_POST,0); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    // Añadido: seguir redirecciones y desactivar verificación SSL (necesario en algunos entornos Vercel)
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); 
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch,CURLOPT_HEADER,false);
    $salida= curl_exec($ch);
    // curl_close() eliminado ya que es redundante para variables locales.
    return $salida;
}