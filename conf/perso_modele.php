<?php
/**
 * Fichier de configuration
 * Ce fichier est appelé après config.php Il en surcharge donc les valeurs. Toutes les modifications au fichier de configuration
 * doivent être faites dans perso.php et jamais dans config.php (qui sera réinitialisé à chaque mise à jour)
 * 
 * Lors de la première installation, renommez ce fichier en "perso.php" et renseignez obligatoiremetn les valeurs
 * de la rubrique "A MODIFIER ABSOLUMENT"
 * 
 * Lors des mises à jour, sauvegardez bien ce fichier (perso.php) et replacez le dans le répetoire
 * "conf"
 * 
 * */

/** ************* A MODIFIER ABSOLUMENT ***************/
// A Décommenter et modifier si vous souhaitez intégrer le log PHP aux autres logs de Waterbear
ini_set ("error_log", "__FINALPATH__/LOG/php_log.log"); 
ini_set ("display_errors", "on");

// SQL
// Indiquez les paramètres de votre connexion SQL
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]="__DB_NAME__"; // Nom de la base de données 
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_adresse_db"]="localhost"; // URL du serveur (généralement localhost)
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_login_db"]="__DB_USER__"; // Nom utilisateur mysql
$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_mdp_db"]="__DB_PWD__"; // mdp utilisateur mysql

// CHEMINS : emplacement de l'installation de votre site sur le serveur
$GLOBALS["tvs_global"]["conf"]["ini"]["install_path"]="__FINALPATH__/"; // install_path

// URL de votre site (racine)
$GLOBALS["tvs_global"]["conf"]["ini"]["wb_url"]="https://__DOMAIN__";

// utilisateurs qui ne sont pas définis dans le registre (super admin)
// ATTENTION : changer le mot de passe du super adminsitrateur (login = superadmin)
$GLOBALS["tvs_global"]["conf"]["ini"]["users_system"]["superadmin"]["mdp"]="__PASSWORD__";

/** ************* FIN DU A MODIFIER ABSOLUMENT ***************/


?>