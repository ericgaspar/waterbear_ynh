

<?PHP

/*
 * 
 * NOUVELLE VERSION QUI FONCTIONNE AVEC LE WS "MAISON"
 * 
 * 
 */

class geoportail {

var $departements=array(); // array (44, 45, 46)
var $centre_x="";
var $centre_y="";
    
function __construct($parametres) {
    $this->departements=$parametres["departements"];
    $centre=$parametres["centre"];

    $tmp=explode(",", $centre);
    $this->centre_y=$tmp[0];
    $this->centre_x=$tmp[1];
}

function completion_ws ($parametres) {

    
    $q=urlencode($parametres["texte"]);
    $limit=$parametres["maximumResponses"];
    $terr=$parametres["terr"];
    
    $url="http://new.moccam-en-ligne.fr/metawb/ws_ban.php?operation=autocomplete&q=$q&postalcodes=$terr&limit=$limit";
    
    $opts = array('http'=>array('method'=>"GET",'timeout'=>"5"));
    $context = stream_context_create($opts);
    $reponse = file_get_contents($url, false, $context);
    
//print ("\n\n $url \n\n");
//print ($reponse);
//die("");    
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $toto=$json->decode($reponse);
    return($toto);
    
}


function completion ($parametres) {
    $propositions=array();
    $texte=$parametres["texte"];
    $tmp=$this->completion_ws(array("texte"=>$texte, "maximumResponses"=>20, "terr"=>$this->departements));
   
    $tmp=$tmp["results"];
//print_r($tmp);
    $nb_propositions=count($tmp);
    foreach ($tmp as $proposition) {
        // on calcule la distance au centre de la carte
        $distance=$this->teste_distance(array("lat"=>$proposition["y"], "lng"=>$proposition["x"]), array("lat"=>$this->centre_y, "lng"=>$this->centre_x));
        $proposition["distance"]=$distance;
        $classification=$proposition["classification"];
        //if ($classification==6 OR $classification==7) { // les codes ont d� changer, mais impossible de trouver une doc l� dessus :/
            array_push($propositions, $proposition);
        //}
    }
    
    // trier le tableau par distance
    $distances=array();
    foreach ($propositions as $idx=>$proposition) {
        $distances[$idx]=$proposition["distance"];
    }
    asort($distances);
    $propositions2=array();
    foreach ($distances as $idx => $bidon) {
        array_push($propositions2, $propositions[$idx]);
    }
    
//print_r($propositions2);

    return ($propositions2);
    

}

function teste_distance ($bib, $bib2) {
    $lat1=(float)$bib["lat"];
    $lat2=(float)$bib2["lat"];
    $lng1=(float)$bib["lng"];
    $lng2=(float)$bib2["lng"];  
    $earth_radius = 6378137;   // Terre = sph�re de 6378km de rayon
    $rlo1 = deg2rad($lng1);
    $rla1 = deg2rad($lat1);
    $rlo2 = deg2rad($lng2);
    $rla2 = deg2rad($lat2);
    $dlo = ($rlo2 - $rlo1) / 2;
    $dla = ($rla2 - $rla1) / 2;
    $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
    $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return (round($earth_radius * $d / 1000));
}

function geocode ($no, $rue, $ville, $CP) {
    $no= urlencode($no);
    $rue= urlencode($rue);
    $ville= urlencode($ville);
    $CP= urlencode($CP);
    $url="http://new.moccam-en-ligne.fr/metawb/ws_ban.php?operation=geocode&no=$no&rue=$rue&ville=$ville&CP=$CP";
 
    $opts = array('http'=>array('method'=>"GET",'timeout'=>"5"));
    $context = stream_context_create($opts);
    $reponse = file_get_contents($url, false, $context);
    
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $toto=$json->decode($reponse);

    $coordonnees=$toto["result"]["lat"]." ".$toto["result"]["lon"];
    return ($coordonnees);
    
}

} // fin de la classe
//ign_ws(array("url1"=>"http://wxs.ign.fr", "url2"=>"ols/apis/completion", "clef"=>"uru7xuf49krvn25sddefop6w", "texte"=>"r pierre po", "maximumResponses"=>"20", "type"=>"StreetAddress", "referer"=>"http://waterbear.info/toto.php", "terr"=>"44"));


?>