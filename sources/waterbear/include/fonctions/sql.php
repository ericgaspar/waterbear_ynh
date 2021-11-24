<?PHP

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_query($parametres) {

$sql=$parametres["sql"];
$contexte=$parametres["contexte"];
$time_start=microtime(true);
if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli
    $resultat=$GLOBALS["mysqli"]["db"]->query($sql);
} else { // ancienne api mysql
    $resultat=mysql_query($sql);
}

$time_stop=microtime(true);
$duree=$time_stop-$time_start;
if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli
    if ($resultat) {
            tvs_log("sql_querys", "REQUETE", array($sql, $contexte, $duree));
            return($resultat);
    } else {
            tvs_log_txt("sql_errors", array($sql, $contexte, $GLOBALS["mysqli"]["db"]->errno, $GLOBALS["mysqli"]["db"]->error));
            throw new tvs_exception("SQL/div", array("contexte"=>$contexte));
    }
    
} else { // ancienne api mysql
    if ($resultat) {
            tvs_log("sql_querys", "REQUETE", array($sql, $contexte, $duree));
            return($resultat);
    } else {
            tvs_log_txt("sql_errors", array($sql, $contexte, mysql_errno(), mysql_error()));
            throw new tvs_exception("SQL/div", array("contexte"=>$contexte));
    }
}

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_as_array ($parametres) {
  	$sql=$parametres["sql"];
	$contexte=$parametres["contexte"];
  	$retour=array();
  	$resultat=sql_query(array("sql"=>$sql, "contexte"=>$contexte));
        if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli) {
            while ($ligne=$resultat->fetch_assoc()) {
                array_push ($retour, $ligne);
            }
        } else { // ancienne api mysql
            while ($ligne=mysql_fetch_assoc($resultat)) {
                array_push ($retour, $ligne);
            }
        }
	return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_as_value ($parametres) {
  	$sql=$parametres["sql"];
	$contexte=$parametres["contexte"];
  	$retour="";
  	$resultat=sql_query(array("sql"=>$sql, "contexte"=>$contexte));
        if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli) {
            if ($ligne=$resultat->fetch_array()) {
                    $retour=$ligne[0];
            }
        } else { // ancienne api mysql
            if ($ligne=mysql_fetch_array($resultat)) {
                    $retour=$ligne[0];
            }
        }
  	return ($retour);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// S�curise des arguments pour inclure dans une requ�te SQL
// tient compte du param�tre 'magic_quotes' du PHP.INI
// R�cursif pour les array
function secure_sql ($asecuriser) {
	if (is_array($asecuriser)) {
	  	$retour=array();
	  	foreach ($asecuriser as $idx=>$valeur) {
		    $retour[$idx]=secure_sql($valeur);
		}
		return($retour);
	}
	
	// Si ce n'est pas une array...
	// Si magic_quotes = ON, on enl�ves les slashes
	if (get_magic_quotes_gpc()==1) {
	  	$asecuriser=stripslashes($asecuriser);
	}
        if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli) {
            $asecuriser=$GLOBALS["mysqli"]["db"]->real_escape_string($asecuriser);
        } else { // ancienne api mysql
            $asecuriser=mysql_real_escape_string($asecuriser);
        }
	return($asecuriser);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_insert_id () {
     if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli) {
         return($GLOBALS["mysqli"]["db"]->insert_id);
     } else { //ancienne api
        return (mysql_insert_id ());
     }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sql_fetch_assoc ($resultat) {
    if ($GLOBALS["tvs_global"]["conf"]["ini"]["bool_mysqli"]==1) { //mysqli)
        $ligne=$resultat->fetch_assoc();
    } else { // ancienne api mysql
        $ligne=mysql_fetch_assoc($resultat);
    }
    return ($ligne);
}

?>