<?php

/* 
 * Ce plugin retourne les coordonnées d'une adresse en utilisant le WS maison basé sur le BAN
 * 
 * parametres :
 * [no]=> N) de la rue (non utilisé pour le moment)
 * [rue]
 * [CP]
 * [ville]
 * 
 * retour
 * [coordonnees] (lat lon) (séparés par un espace)
 * [lat]
 * [lon]
 */

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/geoportail.php");

function plugin_div_geocode ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    $retour["resultat"]["lat"]="";
    $retour["resultat"]["lon"]="";
    $retour["resultat"]["coordonnees"]="";
    
    $no=$parametres["no"];
    $rue=$parametres["rue"];
    $ville=$parametres["ville"];
    $CP=$parametres["CP"];
    
    $geoportail=new geoportail();
    $coordonnees=$geoportail->geocode($no, $rue, $ville, $CP);
    
    $tmp=explode (" ", $coordonnees);
    if (count ($tmp)==2) {
        $retour["resultat"]["lat"]=$tmp[0];
        $retour["resultat"]["lon"]=$tmp[1];
        $retour["resultat"]["coordonnees"]=$coordonnees;
    }
    
    
    
    return ($retour);
}

