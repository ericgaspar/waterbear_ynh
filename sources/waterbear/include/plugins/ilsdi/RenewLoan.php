<?php

function plugin_ilsdi_RenewLoan ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $patronId=$_REQUEST["patronId"];
    $itemId=$_REQUEST["itemId"]; 
    
    $plugin_prolonger_pret=$parametres["plugin_prolonger_pret"];
    $plugin_get_pret=$parametres["plugin_get_pret"];
    
    $erreur="";
    
    // 1) on récupère ID_pret à partir de ID_exemplaire et ID_lecteur
    $tmp=applique_plugin($plugin_get_pret, array("ID_lecteur"=>$patronId, "ID_exemplaire"=>$itemId));
    if ($tmp["succes"]!=1) {
        $erreur="impossible de trouver ce prêt";    
    }
    $notices=$tmp["resultat"]["notices"];
    $nb_notices=$tmp["resultat"]["nb_notices"];
    
    if ($nb_notices == 0) {
        $erreur="impossible de trouver ce prêt";
    } else {
        $ID_pret=$notices[0]["ID"];
        $titre=htmlspecialchars($notices[0]["a_titre_biblio"], ENT_XML1, 'UTF-8');
        // 2) on prolonge le prêt
        $tmp=applique_plugin($plugin_prolonger_pret, array("ID_pret"=>$ID_pret));
        if ($tmp["succes"]!=1) {
            $erreur="impossible de trouver ce prêt"; 
        }
        if ($tmp["resultat"]["message"] != "") {
            $erreur=$tmp["resultat"]["message"]; 
        }
    }
    
    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml.="<RenewLoan>\n";
    if ($erreur!="") {
        $xml.="<error>$erreur</error>\n";
    } else {
        $xml.="<title>$titre</title>\n";
    }
    $xml.="</RenewLoan>\n";
    $retour["resultat"]["xml"]=$xml;
    return ($retour);
        
        
}
    
    

    
    
?>