<?php
$retour=array();
$retour["succes"]=1;
$retour["resultat"]=array();


header('Content-type: application/xml');

$plugin_ilsdi=$GLOBALS["affiche_page"]["parametres"]["plugin_ilsdi"];
$tmp=applique_plugin($plugin_ilsdi, array());
if ($tmp["succes"] != 1) {
    die ($tmp["erreur"]); // quelle mani�re unifi�e de renvoyer une erreur ??
}
$xml=$tmp["resultat"]["xml"];

print($xml);
?>