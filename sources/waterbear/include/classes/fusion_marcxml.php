<?php

include_once($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."classes/tvs_marcxml.php");

/**
 * fusion_marcxml
 * 
 * @package WaterBear
 * @author Quentin CHEVILLON
 * @copyright 2011
 * @version $Id$
 * @access public
 * 
 * @param [notice_a] et [notice_b] => des notices tvs_marcxml
 * @param [type_objet]
 * @param [ID_notice] ** opt **
 */
class fusion_marcxml  {

var $notice_a;
var $notice_b;
var $notice_resultat;
var $filtre;
var $type_objet;
var $ID_notice;

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
   
function __construct ($parametres) {
    $this->notice_a=$parametres["notice_a"];
    $this->notice_b=$parametres["notice_b"];
    $this->type_objet=$parametres["type_objet"];
    $this->ID_notice=$parametres["ID_notice"];
    $this->notice_resultat=new tvs_marcxml(array("type_objet"=>$this->type_objet, "ID_notice"=>$this->ID_notice));
} 

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * fusion_marcxml::nettoie_notice()
 * 
 * $filtre => [defaut_champ]=>garder | supprimer
 *            [defaut_ss_champ] => garder | supprimer
 *            SOIT [champs][110,200,210...][action]=>garder|supprimer 
 *            SOIT [champs][110,200,210...][ss_champs][a,b,c...][action]=>garder|supprimer
 * 
 * @param mixed $notice
 * @param mixed $filtre
 * @return void
 */
function nettoie_notice ($notice, $filtre) {
    $liste_champs=$notice->get_champs("", "");
    foreach ($liste_champs as $champ) {
        $action_champ="";
        $nom_champ=$notice->get_nom_champ($champ);
        
        // on d�termine l'action � effecter (garder champ, suppr champ, niveau des ss champs)
        if (isset($filtre["champs"][$nom_champ]["action"])) {
            $action_champ=$filtre["champs"][$nom_champ]["action"];
        } elseif (is_array($filtre["champs"][$nom_champ]["ss_champs"])) {
            // on ne fait rien
        } else {
            $action_champ=$filtre["defaut_champ"];
        }
        
        if ($action_champ=="garder") {
            // on ne fait rien
        } elseif ($action_champ == "supprimer") {
            $notice->delete_champ($champ);
        } else { // action au niveau des ss_champs
            $liste_ss_champs=$notice->get_ss_champs($champ, "", "", "");
            foreach ($liste_ss_champs as $ss_champ) {
                $nom_ss_champ=$notice->get_nom_ss_champ($ss_champ);
                $action_ss_champ="";
                if (isset($filtre["champs"][$nom_champ]["ss_champs"][$nom_ss_champ]["action"])) {
                    $action_ss_champ=$filtre["champs"][$nom_champ]["ss_champs"][$nom_ss_champ]["action"];
                } else {
                    $action_ss_champ=$filtre["defaut_ss_champ"];
                }
                
                if ($action_ss_champ == "garder") {
                    // on ne fait rien
                } else {
                    $notice->delete_ss_champ($champ, $ss_champ);
                }
            }
        }
    }
}
    
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////    

/**
 * fusion_marcxml::fusionne_notices()
 * 
 * Ce plugin va fusionner les 2 notices this->notice_a et $this->notice_b en utilisant les informations fournies dans $filtre (cf ci-dessous)
 * 4 stat�gie de fusion existent pour chaque champ :
 * > ajouter : le champ de la notice b est ajout� � la notice a quoi qu'il arraive
 * > remplacer : on regarde si un tel champ existe d�j� dans la notice a (cf. d�doublonnage). Si oui, le champ de la notice b remplace celui de la notice a. Sinon, il est simplement ajout�
 * > remplacer_sauf_vide : comme remplacer mais à condition que le champ remplaçant ait au moins un sous-champ non vide (pour éviter les champs vides par défaut)
 * > ajouter_si_existe_pas : on regarde si un tel champ existe d�j� dans la notice a. Si oui, on ne fait rien. Si non, on ajoute le champ
 * > inserer : on regarde si un tel champ existe d�j� dans la notice a. Si oui, on va rajouter les ss-champs de la notice b vers la notice a (cf plus bas). Sinon, on ajouter simplement le champ
 * > supprimer : on ne fait rien
 * 
 * D�doublonnage : Il peut se faire simplement par le nom du champ ou selon des crit�res plus complexes
 * 
 * Insertion : dans la strat�gie 4 (insertion) les sous-champs de la notice b sont ajout�s au champ de la notice a. l� encore, plusieurs strat�gies sont possibles.
 * Comme pour les champs, les ss-champs peuvent �tre ajout�s 1) quoi qu'il arrive, 2) seulement si la notice a n'a pas ces ss-champs, 3) en remplacement de ss-champs identiques
 * 
 * @param mixed $filtre
 * @param [defaut_champ] => action par d�faut pour tous les champs (ajouter | remplacer | ajouter_si_existe_pas | inserer)
 * @param [defaut_ddbl] => d�doublonnage par d�faut pour les champs (nom_champ | criteres | rien)
 * @param [champs][210, 700, 676...][champs_similaires] => 995,997 (champs séparés par des virgules qui doivent être dédoublonnés ensemble (ex. 995 et 997)
 * @param [champs][210, 700, 676...][ddbl]
 * @param [champs][210, 700, 676...][action]
 * @param [champs][210, 700, 676...][ss_champ_ddbl] => Pour le cas o� on aurait un ddbl de type "criteres", on met ici le sous-champ de d�doublonnage. Ex si on met 3, il d�doublonnera les champs dont les $3 sont identiques
 * @param [champs][210, 700, 676...][defaut_ss_champs] => action par d�faut pour les ss-champs de ce champ en cas d'insertion
 * @param [champs][210, 700, 676...][ss_champs][a,b,c...][action] => (ajouter | remplacer | ajouter_si_existe_pas | rien)
 * 
 * @return void
 */
function fusionne_notices ($filtre) {
     $liste_champs=$this->notice_b->get_champs("", "");
     foreach ($liste_champs as $champ) {
        $nom_champ=$this->notice_b->get_nom_champ($champ);
        $action_champ=$filtre["defaut_champ"];
        $type_ddbl=$filtre["defaut_ddbl"];
        $liste_ss_champs=array();
        
        // 0) on d�termine les actions souhait�es pour ce champ
        if (isset($filtre["champs"][$nom_champ])) {
            $champs_similaires_str=$filtre["champs"][$nom_champ]["champs_similaires"];
            $champs_similaires=explode(",", $champs_similaires_str);
            foreach ($champs_similaires as $idx=>$valeur) {
                $champs_similaires[$idx]=trim($valeur);
            }
            $action_champ=$filtre["champs"][$nom_champ]["action"];
            $type_ddbl=$filtre["champs"][$nom_champ]["ddbl"];
            $filtre_ss_champs=$filtre["champs"][$nom_champ]["ss_champs"];
            $defaut_ss_champ=$filtre["champs"][$nom_champ]["defaut_ss_champ"];
            $ss_champ_ddbl=$filtre["champs"][$nom_champ]["ss_champ_ddbl"];
            if ($action_champ == "") {
                $action_champ = $filtre["defaut_champ"];
            }
            if ($type_ddbl == "") {
                $type_ddbl = $filtre["defaut_ddbl"];
            }
        } 
        
        // 1) d�doublonnage
        if ($type_ddbl == "nom_champ") {
            $liste_ddbl=$this->notice_a->get_champs($nom_champ, "");
            if (is_array($champs_similaires)) {
                foreach($champs_similaires as $champ_similaire) {
                    if ($champ_similaire==="") {
                        continue;
                    }
                    $tmp2=$this->notice_a->get_champs($champ_similaire, "");
                    foreach ($tmp2 as $elem) {
                        array_push($liste_ddbl, $elem);
                    }
                }
            }
        } elseif ($type_ddbl == "criteres") {
            $tmp=$this->notice_a->get_champs($nom_champ, ""); // on prend les champs de m�me nom
            if (is_array($champs_similaires)) {
                foreach($champs_similaires as $champ_similaire) {
                    $tmp2=$this->notice_a->get_champs($champ_similaire, "");
                    foreach ($tmp2 as $elem) {
                        array_push($tmp, $elem);
                    }
                }
            }
            $liste_ddbl=array();
            
            // on cherche la valeur du ss-champ de comparaison de la notice b
            $ss_champ_compare=$this->notice_b->get_ss_champs($champ, $ss_champ_ddbl, "", 0);
            if (count($ss_champ_compare) > 0) {
                $valeur_ss_champ_compare=$this->notice_b->get_valeur_ss_champ($ss_champ_compare[0]);
                
                // Pour chaque champ de m�me nom de la notice a, on regarde si un ss-champ de m�me nom et de m�me valeur existe
                foreach ($tmp as $champ_test) { // pour chaque champ � comparer
                    $ss_champ_test=$this->notice_a->get_ss_champs($champ_test, $ss_champ_ddbl, $valeur_ss_champ_compare, 0);
                    
                    if (count($ss_champ_test) > 0) {
                        array_push ($liste_ddbl, $champ_test);
                    }
                }
            }
    
        }
        
        // 2) actions
        if ($action_champ == "ajouter") {
            $definition=$this->notice_a->champ_2_definition($champ);
            $this->notice_a->add_champ($nom_champ, $definition, "");
        } elseif ($action_champ == "remplacer" OR $action_champ == "remplacer_sauf_vide") {
            $definition=$this->notice_a->champ_2_definition($champ); // dénition du champ remplaçant
            if (count($liste_ddbl) == 0) {
                $this->notice_a->add_champ($nom_champ, $definition, "");
            } else {
                 
                // On regarde si le champ remplaçant contient au moins un sous-champ non vide
                $bool_au_moins_un_ss_champ_non_vide=0;
                foreach ($definition as $tmp) {
                    if ($tmp["valeur"] != "") {
                        $bool_au_moins_un_ss_champ_non_vide=1;
                    }
                }
                
                // on fait le remplacement seulement si action="remplacer" ou ="remplacer_sauf_vide" et qu'on a au moins un ss champ non vide
                if ($action_champ == "remplacer" OR ($action_champ == "remplacer_sauf_vide" AND $bool_au_moins_un_ss_champ_non_vide==1)) {
                    foreach ($liste_ddbl as $doublon) {
                        $this->notice_a->reset_champ($doublon, $definition);
                    }
                }
            }
        } elseif ($action_champ == "ajouter_si_existe_pas") {
             if (count($liste_ddbl) == 0) {
                $definition=$this->notice_a->champ_2_definition($champ);
                $this->notice_a->add_champ($nom_champ, $definition, "");
             }
        } elseif ($action_champ == "inserer") {
            if (count($liste_ddbl) == 0) { // si pas de doublon, on se contente d'ajouter'
                $definition=$this->notice_a->champ_2_definition($champ);
                $this->notice_a->add_champ($nom_champ, $definition, "");
            } else { // si doublon, on ins�re les ss-champs
                foreach ($liste_ddbl as $doublon) {
                    // Quelle action appliquer ?
                    $liste_ss_champs=$this->notice_b->get_ss_champs($champ, "", "", "");
                    foreach ($liste_ss_champs as $ss_champ) {
                        $action_ss_champ=$defaut_ss_champ;
                        $nom_ss_champ=$this->notice_b->get_nom_ss_champ($ss_champ);
                        $valeur_ss_champ=$this->notice_b->get_valeur_ss_champ($ss_champ);
                        if (isset($filtre_ss_champs[$nom_ss_champ])) {
                            $action_ss_champ=$filtre_ss_champs[$nom_ss_champ]["action"];
                        }
                    
                        // ddbl ss_champ : est-ce que ce ss-champ est d�j� pr�sent dans la notice
                        $liste_ddbl_ss_champs=$this->notice_a->get_ss_champs($doublon, $nom_ss_champ, "", "");
                        
                        
                        // Actions ss-champs
                        if ($action_ss_champ == "ajouter") {
                            $this->notice_a->add_ss_champ($doublon, $nom_ss_champ, $valeur_ss_champ, "");
                        } elseif ($action_ss_champ == "ajouter_si_existe_pas") {
                            if (count($liste_ddbl_ss_champs) == 0) {
                                $this->notice_a->add_ss_champ($doublon, $nom_ss_champ, $valeur_ss_champ, "");
                            } else {
                                // on ne fait rien
                            }
                        } elseif ($action_ss_champ == "remplacer") {
                           if (count($liste_ddbl_ss_champs) == 0) { // si aucun ss-champ identique trouv�, on se contente d'ajouter'
                                $this->notice_a->add_ss_champ($doublon, $nom_ss_champ, $valeur_ss_champ, "");
                            } else { // sinon, pour chaque ss-champ identique, on maj la valeur
                                foreach ($liste_ddbl_ss_champs as $doublon_ss_champ) {
                                    $this->notice_a->update_ss_champ($doublon_ss_champ, $valeur_ss_champ);
                                }
                            }
                        } else { // supprimer
                            // sinon, on ne fait rien...
                        }
                    } 
                }
            }
            
            
        } else {
            // sinon on ne fait rien...
        }
     }
     return ($this->notice_a);
}    
    
    
    
    
    
    
    
    
    
    
} // fin de la classe


?>