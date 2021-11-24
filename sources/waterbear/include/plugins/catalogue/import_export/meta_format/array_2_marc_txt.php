<?php

/**
 * plugin_catalogue_import_export_meta_format_array_2_marc_txt()
 * 
 * G�n�re une chaine de caract�re unimarc txt
 * Il nettoie �galement les champs / ss-champs vides.
 * On peut utiliser le param�tre $ss_champs_non_significatifs pour indiquer les ss-champs qui ne doivent pas �tre pris en compte m�me si non vides (par ex. 700$4)
 * Sous la forme [ss_champs_non_significatifs][700_4|701_4|...]=>1
 * 
 * 
 * @param mixed $parametres
 * @return ["texte"] => la notice formatée au format texte
 */
function plugin_catalogue_import_export_meta_format_array_2_marc_txt ($parametres) {
    $retour=array();
    $retour["succes"]=1;
    $retour["erreur"]="";
    $retour["resultat"]=array();
    $retour["resultat"]["texte"]="";
    
    $notice=$parametres["notice"]; // d�finition de la notice sous forme d'array
    $ss_champs_non_significatifs=$parametres["ss_champs_non_significatifs"];
    
    $avant_notice=$parametres["avant_notice"]; // avant tout le reste
    $avant_champ=$parametres["avant_champ"]; // avant le nom du champ
    $avant_ss_champ=$parametres["avant_ss_champ"]; // avant le nom du sous-champ
    $avant_indicateurs=$parametres["avant_indicateurs"]; // avant les indicateurs
    $apres_notice=$parametres["apres_notice"]; // après tout le reste
    $apres_champ=$parametres["apres_champ"]; // après le nom du champ
    $apres_ss_champ=$parametres["apres_ss_champ"]; // après la valeur du ss-champ
    $apres_indicateurs=$parametres["apres_indicateurs"]; // après les indicateurs
    $apres_nom_ss_champ=$parametres["apres_nom_ss_champ"]; // entre le nom du sous-champ et sa valeur
    $avant_champ_seul=$parametres["avant_champ_seul"]; // avant le contenu d'un champ sans sous-champs
    $apres_champ_seul=$parametres["apres_champ_seul"]; // après le contenu d'un champ sans sous-champs
    
    $bool_masquer_champs_non_conformes=$parametres["bool_masquer_champs_non_conformes"];
    
    $retour["resultat"]["texte"]=$avant_notice;

    
	$idx_champs=0; // nb de champs. Si toujours 0 � la fin, notice vide, on ne renvoie rien
	
	// Pour chaque champ...
	foreach ($notice["champs"] as $champ) { // pour cahque champ
            $bool_exporte=1;
            $string_champ="";//on mettra en fait les ss-champs
            
            // gestion des indicateurs
            if ($champ["id1"]=="") {
                    $champ["id1"]=" ";
            }
            if ($champ["id2"]=="") {
                    $champ["id2"]=" ";
            }

            

            // SI sous-champs...
            if (count($champ["ss_champs"]) > 0) {
                $bool_exporte=0;		
                foreach($champ["ss_champs"] as $sous_champ) { // Pour chaque sous-champ...	
                    if (strlen($sous_champ["nom"]) != 1) { // gestion des ss-champs non conformes (ex. $9a...)
                        if ($bool_masquer_champs_non_conformes==1) {
                            continue;
                        }
                    }
                    if ($sous_champ["valeur"] != "" AND $sous_champ["valeur"] != "_void_") {
                        $string_champ.=$avant_ss_champ.$sous_champ["nom"].$apres_nom_ss_champ.$sous_champ["valeur"].$apres_ss_champ;
                        if ($ss_champs_non_significatifs[$champ["nom"]."_".$sous_champ["nom"]] != 1) {
                            $bool_exporte=1;
                        }
                    }
                } // fin du pour cahque ss-champ
            } else {// SI Champ sans sous-champs...
                //$string_champ=$separateur_champs.$champ["texte_champ"];
                $bool_exporte=1; // ??? todo � modifier ?
                $string_champ=$avant_champ_seul.$champ["valeur"].$apres_champ_seul;
            }
		
		// Pour chaque champ, on va calculer les pointeurs (nom du champ, nbre de caract�res du champ et adresse de d�but du champ)
            if ($bool_exporte == 1 AND ((strlen($champ["nom"])==3 AND is_numeric($champ["nom"])) OR $bool_masquer_champs_non_conformes!=1)) {
                $retour["resultat"]["texte"].=$avant_champ.$champ["nom"].$apres_champ.$avant_indicateurs.$champ["id1"].$champ["id2"].$apres_indicateurs.$string_champ;
            }
	} // fin du 'pour chaque champ...'
	
	$retour["resultat"]["texte"].=$apres_notice;


    return($retour);
}

?>