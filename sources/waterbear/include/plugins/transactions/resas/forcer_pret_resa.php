<?php
/**
 * 
 * Ce plugin maj une notice de résa qui était affectée à un lecteur X quand on force le prêt pour un lecteur Y
 * 
 * on récupère les érsas concernées via le [plugin_get_resa] (i.e code etat de la resa=25  ID exe = xx et id lecteur != xx
 * puis on maj et on enregistre la notice
 * 
 * 
 * [bureau]["infos_exemplaire"]["ID"] => ID de l'exemplaire
   [bureau]["infos_lecteur"]["ID"] => ID du lecteur
 * [plugin_get_resa] : recherche la rés
 * [plugin_maj_resa] : maj la resa
 * [plugin_notice_2_db] : enregistre dans la db
 * 
 */

function plugin_transactions_resas_forcer_pret_resa ($parametres) {

    $retour=array();
    $retour["succes"]=1;
    $retour["resultat"]=array();
    
    // paramètres   
    $bureau=$parametres["bureau"];
    $plugin_get_resa=$parametres["plugin_get_resa"];
    $plugin_maj_resa=$parametres["plugin_maj_resa"];
    $plugin_notice_2_db=$parametres["plugin_notice_2_db"];
    $ID_exemplaire=$bureau["infos_exemplaire"]["ID"];
    $ID_lecteur=$bureau["infos_lecteur"]["ID"];
    
    // 1) on récupère la résas concernée s'il y en a (normalement il ne peut pas y en avoir plusieurs, mais au cas où...)
    $tmp=applique_plugin($plugin_get_resa, array("ID_exemplaire"=>$ID_exemplaire, "ID_lecteur"=>$ID_lecteur));
    if ($tmp["succes"]!=1) {
        return($tmp);
    }
    $lignes=$tmp["resultat"]["notices"];
    $nb_notices=$tmp["resultat"]["nb_notices"];
    
    // 2) on maj la résa
    if ($nb_notices > 0) {
        foreach ($lignes as $ligne) {
            $notice=$ligne["xml"];
            $ID_notice=$ligne["ID"];
            $tmp=applique_plugin($plugin_maj_resa, array("notice"=>$notice));
            if ($tmp["succes"]!=1) {
                return($tmp);
            }
            $notice=$tmp["resultat"]["notice"];
            $retour=applique_plugin($plugin_notice_2_db, array("notice"=>$notice, "ID_notice"=>$ID_notice));
            
        }
    }
    
    return ($retour);
    
}

