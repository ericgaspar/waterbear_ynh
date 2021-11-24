<?php
/**
 * plugin_catalogue_marcxml_db_notice_2_db()
 * 
 * Ce plugin cr�e ou met � jour une notice dans la base de donn�es
 * La notice doit �tre fournie en XML
 * Il faut �galement fournir les plugins n�cessaires pour g�rer les acc�s, les liens et faire les op�rations SQL � proprement parler
 * 
 * @param array $parametres
 * @param [plugins_modif_notice] => [0,1,2...][nom_plugin|parametres...] liste de plugins pour modifier la notice avant de l'enregistrer (optionnel)
 * @param [plugin_notice_2_infos] => le plugin pour r�cup�rer acc�s, tris, liens...
 * @param [plugin_maj_liens_implicites] => pour maj les liens implicites
 * @param [plugin_crea_notice] => op�rations SQL
 * @param [notice] => notice DOMXml
 * @param [ID_notice] ** option ** => si maj de notice
 * @param [type_objet] => type de notice (biblio, auteur...)
 * 
 * @return [ID_notice]
 */
function plugin_catalogue_marcxml_db_notice_2_db($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    //$parametres=plugins_2_param($parametres, array()); // utilise les !! et les ?? 
    
    $notice=$parametres["notice"];
    $ID_notice=$parametres["ID_notice"];

    
    $bool_creation=1; // par d�faut cr�ation de notice...
    if ($ID_notice!="") {
        $bool_creation=0; // ... sauf si on a un ID
    } 
    
    // Si cr�ation de notice, on cr�e une notice vide bidon pour r�cup�rer un ID
    // c'est n�cessaire pour g�n�rer le champ 000 avec le n� de notice
    // lui m�me n�cessaire pour la colonne a_unimarc : notice en iso2709 pr�te pour l'export vers Bokeh
    if ($bool_creation==1){
        $sql="insert into obj_".$parametres["type_objet"]."_acces (ID) values ('')";
        sql_query(array("sql"=>$sql, "contexte"=>"notice_2_db.php::cr�ation notice vide"));
        $ID_notice=sql_insert_id();
        $notice=add_champ_000($notice, $ID_notice, $parametres["type_objet"]);
    }
    
    // 0) ** option ** modifications de la notice avant de l'enregistrer
    if (is_array($parametres["plugins_modif_notice"])) {
        foreach ($parametres["plugins_modif_notice"] as $plugin_modif_notice) {
            $tmp=applique_plugin($plugin_modif_notice, array("notice"=>$notice));
            if ($tmp["succes"]==0) {
                return ($tmp);
            }
        }
        $notice=$tmp["resultat"]["notice"];
    }
    
    // 1) On r�cup�re acc�s, tris, contenu, liens
    $tmp=applique_plugin($parametres["plugin_notice_2_infos"], array("notice"=>$notice));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $acces=$tmp["resultat"]["acces"];
    $tri=$tmp["resultat"]["tri"];
    $contenu=$tmp["resultat"]["contenu"];
    $liens=$tmp["resultat"]["liens"];
    
  
    // 2) On met � jour les liens Implicites (seulement si modification)
    if ($ID_notice != "" AND is_array($parametres["plugin_maj_liens_implicites"]) AND $bool_creation==0) {
        $tmp=applique_plugin($parametres["plugin_maj_liens_implicites"], array("ID"=>$ID_notice, "type"=>$parametres["type_objet"], "notice_new"=>$notice));
        if ($tmp["succes"]==0) {
            return ($tmp);
        }
    }
    
    
    // 3) On enregistre la notice dans la DB
    $tmp=applique_plugin($parametres["plugin_crea_notice"], array("contenu"=>$contenu, "acces"=>$acces, "tri"=>$tri, "type"=>$parametres["type_objet"], "liens"=>$liens, "ID"=>$ID_notice));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    $ID_notice=$tmp["resultat"]["ID_notice"];
    
    $retour["resultat"]["ID_notice"]=$ID_notice;
    
    // 4) si cr�ation de notice, on g�n�re un champ 000 et on r�-enregistre
    // NON !! c'est le plugin crea_notice qui g�re �a !!
    /**
    if ($bool_creation == 1) {
        $notice=add_champ_000($notice, $ID_notice, $parametres["type_objet"]);
        $contenu=$notice->saveXML();
    }
    $tmp=applique_plugin($parametres["plugin_crea_notice"], array("contenu"=>$contenu, "acces"=>$acces, "tri"=>$tri, "type"=>$parametres["type_objet"], "liens"=>$liens, "ID"=>$ID_notice));
    if ($tmp["succes"]==0) {
        return ($tmp);
    }
    **/
    
    return ($retour);    
} // fin du plugin



?>