<?PHP

$nom_noeud=$_REQUEST["nom_noeud"];
$valeur_noeud=$_REQUEST["valeur_noeud"];


affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("nom_noeud"=>$nom_noeud, "valeur_noeud"=>$valeur_noeud)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");






?>