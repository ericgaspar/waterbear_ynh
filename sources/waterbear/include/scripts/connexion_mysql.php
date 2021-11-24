<?PHP
// Connexion MySQL
if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli
    $db=new mysqli($GLOBALS["tvs_global"]["conf"]["ini"]["mysql_adresse_db"],$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_login_db"], $GLOBALS["tvs_global"]["conf"]["ini"]["mysql_mdp_db"], $GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]);
    if ($db->connect_errno) {
        die ("Echec lors de la connexion à MySQLi : (" . $db->connect_errno . ") " . $db->connect_error);
    }
    $GLOBALS["mysqli"]["db"]=$db;
  
} else { // ancienne interface mysql
    $db=mysql_connect($GLOBALS["tvs_global"]["conf"]["ini"]["mysql_adresse_db"],$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_login_db"], $GLOBALS["tvs_global"]["conf"]["ini"]["mysql_mdp_db"]) OR die ("Impossible de se connecter � MySQL<br>");
    mysql_select_db ($GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"],$db) OR die ("Impossible de selectionner la DB <br>".$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"]);
}

sql_query(array("sql"=>"SET NAMES 'UTF8'", "connexion_mysql.php"));
$_SESSION["system"]["DB"]=$GLOBALS["tvs_global"]["conf"]["ini"]["mysql_nom_db"];

?>