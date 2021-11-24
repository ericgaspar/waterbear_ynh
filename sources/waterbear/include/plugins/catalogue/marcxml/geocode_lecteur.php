<?php

/* 
 * Ce plugin modifie le 120$l des fiches lecteurs (coordonnées) en se basnt sur les champs 120$a,b,c,d (n°, rue, CP, ville)
 * Il ne modifie que les notices qui ont un champ 120 (donc pas les membres de famille dont les coordonnées sont récupérées de la carte duchef de famille)
 * On peut spéciier une valeur [bool_garde_coordonnees_existantes]=> si vaut 1 on ne remplace pas le 120$l s'il y a déja des coordonnées (et qu'elles ont l'air bien formées)
 * 
 * @param mixed $parametres
 * @param SOIT [notice] => la notice DomXml
 * @param SOIT [tvs_marcxml]
 * @param [plugin_get_no] => récupère le n° (120$a)
 * @param [plugin_get_rue] => récupère la rue (120$b)
 * @param [plugin_get_CP] => récupère le CP (120$c)
 * @param [plugin_get_ville] => récupère la ville (120$d)
 * @param [plugin_get_coordonnes] => récupère les coordonnées existantes (120$l) (pour voir si on doit les garder ou les remplacer)
 * @param [plugin_geolocalise] => récupère les coordonnées
 * @param [plugin_modifie_notice] => modifie la notice
 * @param [bool_garde_coordonnees_existantes] => si vaut "oui" on vérifie si le 120$l existe déjà et est bien formé. Si oui, on ne modifie pas
 */

function plugin_catalogue_marcxml_geocode_lecteur ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    $notice=$parametres["notice"];
    $tvs_marcxml=$parametres["tvs_marcxml"];
    $plugin_get_no=$parametres["plugin_get_no"];
    $plugin_get_rue=$parametres["plugin_get_rue"];
    $plugin_get_CP=$parametres["plugin_get_CP"];
    $plugin_get_ville=$parametres["plugin_get_ville"];
    $plugin_get_coordonnees=$parametres["plugin_get_coordonnees"];
    $plugin_geolocalise=$parametres["plugin_geolocalise"];
    $plugin_modifie_notice=$parametres["plugin_modifie_notice"];
    $bool_garde_coordonnees_existantes=$parametres["bool_garde_coordonnees_existantes"];
    
    // 1) on récupère la notice
    if ($tvs_marcxml == "") {
        $tvs_marcxml=new tvs_marcxml(array());
        $tvs_marcxml->load_notice($notice);
    }
    
    // 2) on récupère les infos
    $tmp=applique_plugin($plugin_get_no, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $no=$tmp["resultat"]["texte"];
    
    $tmp=applique_plugin($plugin_get_rue, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $rue=$tmp["resultat"]["texte"];
    
    $tmp=applique_plugin($plugin_get_CP, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $CP=$tmp["resultat"]["texte"];
    
    $tmp=applique_plugin($plugin_get_ville, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $ville=$tmp["resultat"]["texte"];
    
    $tmp=applique_plugin($plugin_get_coordonnees, array("tvs_marcxml"=>$tvs_marcxml));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $anciennes_coordonnees=$tmp["resultat"]["texte"];
    
    // 3) On teste si coordonnées existantes (si bool_garde_coordonnees_existantes==1)
    if ($bool_garde_coordonnees_existantes == "oui" AND $anciennes_coordonnees != "") {
        $retour["resultat"]["notice"]=$notice;
        return ($retour); // on retourne la notice sans la modifier
    }
    
    // 4) On vérifie qu'on a bien des infos de géolocalisation (sinon on est sûrement sur une carte de membre de famille)
    if ($rue=="" OR ($CP=="" OR $ville=="")) { // il faut qu'on ait une rue et soit un CP soit une ville
        $retour["resultat"]["notice"]=$notice;
        return ($retour); // on retourne la notice sans la modifier
    }
    
    // 5) On géolocalise
    $tmp=applique_plugin($plugin_geolocalise, array("no"=>$no, "rue"=>$rue, "ville"=>$ville, "CP"=>$CP));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    $lat=$tmp["resultat"]["lat"];
    $lon=$tmp["resultat"]["lon"];
    $coordonnees=$tmp["resultat"]["coordonnees"];
    
    // 6) On modifie la notice
    $tmp=applique_plugin($plugin_modifie_notice, array("tvs_marcxml"=>$tvs_marcxml, "lat"=>$lat, "lon"=>$lon, "coordonnees"=>$coordonnees));
    if ($tmp["succes"] != 1) {
        return ($tmp);
    }
    
    // On retourne la notice modifiée
    $retour["resultat"]["notice"]=$tmp["resultat"]["notice"];
    
    
    return ($retour);
}

