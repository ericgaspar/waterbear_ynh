<?php
/**
 * 
 * CE script corrige des tables et des colonnes qui ne sont pas en utf8
 */

$sqls=array();
// BIBLIO
$sqls[]="ALTER TABLE  obj_biblio_liens DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
$sqls[]="ALTER TABLE  obj_biblio_acces DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
$sqls[]="ALTER TABLE  obj_biblio_acces CHANGE  contenu  contenu LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL"; // on modifie aussi le type de TEXT à LONGTEXT
$sqls[]="ALTER TABLE  obj_biblio_acces CHANGE  a_auteurs  a_auteurs TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_biblio_acces CHANGE  a_titres  a_titres TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_biblio_acces CHANGE  a_tousmots  a_tousmots TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_biblio_acces CHANGE  t_auteurs  t_auteurs TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_biblio_acces CHANGE  t_titres  t_titres TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

// AUTEUR
$sqls[]="ALTER TABLE  obj_auteur_liens DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
$sqls[]="ALTER TABLE  obj_auteur_acces DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
$sqls[]="ALTER TABLE  obj_auteur_acces CHANGE  contenu  contenu TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_auteur_acces CHANGE  a_vedette  a_vedette TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_auteur_acces CHANGE  a_tousmots  a_tousmots TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_auteur_acces CHANGE  t_vedette  t_vedette TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_auteur_acces CHANGE  a_type_entree  a_type_entree VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sqls[]="ALTER TABLE  obj_auteur_acces CHANGE  a_entree_principale  a_entree_principale TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

// augmente la taille maxi des notices (cas de notices très longues avec un grand nombre d'exemplaires par exemple)
$sqls[]="ALTER TABLE obj_biblio_acces MODIFY contenu LONGTEXT";

foreach ($sqls as $sql) {
    $resultat=sql_query(array("sql"=>$sql, "contexte"=>"maj46"));
}
