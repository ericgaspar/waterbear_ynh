<?php
/**
 * Ici on redéfinit des fonctions php5 qui ont été supprimées de php7
 * 
 * 
 */

if(!function_exists('ereg'))            { function ereg($pattern, $subject, &$matches = []) { return preg_match('/'.$pattern.'/', $subject, $matches); } }
if(!function_exists('eregi'))           { function eregi($pattern, $subject, &$matches = []) { return preg_match('/'.$pattern.'/i', $subject, $matches); } }
if(!function_exists('ereg_replace'))    { function ereg_replace($pattern, $replacement, $string) { return preg_replace('/'.$pattern.'/', $replacement, $string); } }
if(!function_exists('eregi_replace'))   { function eregi_replace($pattern, $replacement, $string) { return preg_replace('/'.$pattern.'/i', $replacement, $string); } }
if(!function_exists('split'))           { function split($pattern, $subject, $limit = -1) { return preg_split('/'.$pattern.'/', $subject, $limit); } }
if(!function_exists('spliti'))          { function spliti($pattern, $subject, $limit = -1) { return preg_split('/'.$pattern.'/i', $subject, $limit); } }


?>