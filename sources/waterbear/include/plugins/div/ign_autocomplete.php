<?php
include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/geoportail.php");

function plugin_div_ign_autocomplete ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
//return ($retour); // tmp plantage des ws ign 26/01/2021
    
    // dans le registre
    $clef=$parametres["clef"];
    $departements=$parametres["departements"];
    $referer=$parametres["referer"];  
    $centre=$parametres["centre"];
    // script
    $chaine=$parametres["chaine"];
    
    $geoportail=new geoportail(array("clef"=>$clef, "departements"=>$departements, "referer"=>$referer, "centre"=>$centre));
    
    $propositions=$geoportail->completion(array("texte"=>$chaine));
    
    
    $retour["resultat"]["propositions"]=$propositions;
    
    return ($retour);
}



?>