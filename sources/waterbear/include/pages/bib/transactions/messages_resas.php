<?php
$input_panier=$_REQUEST["input_panier"];
$bool_test=$_REQUEST["bool_test"];
$mail_test=$_REQUEST["mail_test"];

$plugin_messages=$GLOBALS["affiche_page"]["parametres"]["plugin_messages"];
$max_execution_time=$GLOBALS["affiche_page"]["parametres"]["max_execution_time"];
ini_set("max_execution_time", $max_execution_time);

$tmp=applique_plugin($plugin_messages, array("panier"=>$input_panier, "bool_test"=>$bool_test, "mail_test"=>$mail_test));
if ($tmp["succes"] == 1) {
    $message=$tmp["resultat"]["message"];
    $log_mails=$tmp["resultat"]["log_mails"];
    $mail_erreurs=$tmp["resultat"]["mail_erreurs"];
}

affiche_template ($GLOBALS["affiche_page"]["template"]["tmpl_squelette"], array("param_tmpl_main"=>array("message"=>$message, "log_mails"=>$log_mails, "mail_erreurs"=>$mail_erreurs)));

include ($GLOBALS["tvs_global"]["conf"]["ini"]["include_path"]."/scripts/affiche_page.php");


?>