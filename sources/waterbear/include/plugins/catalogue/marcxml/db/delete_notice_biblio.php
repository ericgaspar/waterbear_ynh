<?php

/**
 * plugin_catalogue_marcxml_db_delete_notice_biblio()
 * 
 * ATTENTION CE plugin est désormais adapté à d'autres types d'objets
 * 
 * Ce plugin supprime une notice biblio, en v�rifiant qu'il n'y a aucun exemplaire, abo... rattach� � la notice (m�me des exemplaires inactifs)
 * Si OK, on supprimera la notice en appelant un plugin de type delete_notice_autorite qui v�rifiera pour sa part que la notice biblio
 * n'a pas d'objets li�s implicitement (pr�ts, r�sas...)
 * 
 * Ce plugin peut désormais être utilisé pour d'autres objets
 * [type_obj] type d'objet (ex biblio)
 * [objets_lies] [1,2,3] : les liens explicites à vérifier (ex. pour une notice biblio vérifier les exemplaiers et les abonnements)
 *                      [nom_obj] : par ex. exemplaire
 *                      [message] : message d'erreur associé
 * [plugin_delete] : ce plugin assurera la suppression de l'objet et pourra vérifier les liens implicites
 * 
 * @param mixed $parametres
 * @return void
 */
function plugin_catalogue_marcxml_db_delete_notice_biblio ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $ID=$parametres["ID"];
    $plugin_delete=$parametres["plugin_delete"];
    $objets_lies=$parametres["objets_lies"];
    $type_obj=$parametres["type_obj"];
    
    // 1) on regarde si cette notice biblio a des exemplaires (actifs ou non)
    
    foreach ($objets_lies as $objet_lie) {
        $nom_obj=$objet_lie["nom_obj"];
        $message=$objet_lie["message"];
        $sql="select count(*) from obj_".$type_obj."_liens where type_objet='$nom_obj' AND ID=$ID";
        try {
            $nb=sql_as_value(array("sql"=>$sql, "contexte"=>"plugin_catalogue_marcxml_db_delete_notice_biblio::recupere les $nom_obj lies a la notice $type_obj $ID "));
        } catch (tvs_exception $e) {
            $retour["succes"]=0;
            $retour["erreur"]=$e->get_exception();
            return ($retour);
        }

        if ($nb > 0) {
            $retour["succes"]=0;
            $retour["erreur"]=$message;
            return ($retour);
        }
    }
    
    // 2) Si aucun objet li�, on peut supprimer la notice (apr�s avoir v�rifi� qu'aucun lien implicite ne subsiste (pr�t, r�sa...))
    $tmp=applique_plugin ($plugin_delete, array("ID"=>$ID, "type_obj"=>$type_obj));
    return ($tmp);
    
    
    
    
    
}



?>