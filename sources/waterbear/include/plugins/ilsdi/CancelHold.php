<?php
function plugin_ilsdi_CancelHold ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $patronId=$_REQUEST["patronId"];
    $itemId=$_REQUEST["itemId"]; // en fait ID de la notice biblio
    
    $plugin_recherche_resa=$parametres["plugin_recherche_resa"];
    $plugin_delete_resa=$parametres["plugin_delete_resa"];
    
    $code_delete=42; // par défaut 42 = supprimé par lecteur
    
    $erreur="";
    
    // 1) on récupère la résa par l'ID biblio (!!) et l'ID lecteur
    $tmp=applique_plugin($plugin_recherche_resa, array("ID_lecteur"=>$patronId, "ID_biblio"=>$itemId));
    if ($tmp["succes"]!=1) {
        $erreur="impossible de trouver cette réservation";    
    }
    $notices=$tmp["resultat"]["notices"];
    $nb_notices=$tmp["resultat"]["nb_notices"];
    
    if ($nb_notices == 0) {
        $erreur="impossible de trouver cette réservation";
    } else {
        $ID_resa=$notices[0]["ID"];
        $etat=$notices[0]["a_etat"];
        $titre=htmlspecialchars($notices[0]["a_titre_biblio"], ENT_XML1, 'UTF-8');
        
        // 2) on vérifie que la résa n'est pas déjà affectée // a reprendre pour voir si modifiable => compliqué on laisse tomber
        if ($etat =="25") {
            $erreur="Vous ne pouvez supprimer cette réservation car elle est déjà affectée"; 
            //$code_delete=35; // 35 = délai dépassé : générera un affichage dans la liste des socuments à remettre en rayon
        } else {
            // 3) on delete la résa
            $tmp=applique_plugin($plugin_delete_resa, array("ID_resa"=>$ID_resa, "code_delete"=>$code_delete));
            if ($tmp["succes"] != 1) {
                $erreur=$tmp["erreur"];
            }
        }
    }
    
    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml.="<CancelHold>\n";
    if ($erreur!="") {
        $xml.="<error>$erreur</error>\n";
    } else {
        $xml.="<title>$titre</title>\n";
    }
    $xml.="</CancelHold>\n";
    $retour["resultat"]["xml"]=$xml;
    return ($retour);
    
   
    
    
    
}


?>

