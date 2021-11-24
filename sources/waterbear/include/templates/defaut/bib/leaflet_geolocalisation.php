<html>
    <head>
        <title><?PHP print (get_intitule ("", $GLOBALS["affiche_page"]["parametres"]["titre_page"], array()));?></title>
        <link rel="icon" type="image/png" href="<?PHP print($GLOBALS["affiche_page"]["parametres"]["favicon"]) ?>" />  
        <style type="text/css">
            html { height: 100% }
            body { height: 100%; margin: 0; padding: 0 }
            #mapid { height: 100% }
        </style>
        
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin=""/>
        
         <!-- Make sure you put this AFTER Leaflet's CSS -->
         <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
           integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
           crossorigin="">
         </script>
         
         
         <script language="javascript">
             
          var mymap;   
             
         function init() {
            init_map();
            var points=window.opener.get_points(window);
         }
         
        function init_map () {
            mymap = L.map('mapid').setView([<?PHP print ($GLOBALS["affiche_page"]["parametres"]["coordonnees_centre"]);?>], 13);
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'pk.eyJ1IjoibW9jY2FtIiwiYSI6ImNrbGF5MWhteDAxbmkzMm0xeG5xcTIxZXQifQ.tgIZNad-SnO-MX5PgM88XQ'
            }).addTo(mymap);
        }
        
        function affiche_points (points) {
           
            //alert (points);
            var liste_coo=points.split(",");
            liste_coo.push("bidon"); // on rajoute un �l�ment bidon�la fin
            var nb_lecteurs=1;
            for (idx_coo in liste_coo) {
                idx_coo=parseInt(idx_coo);
                var size=<?PHP print ($GLOBALS["affiche_page"]["parametres"]["taille_icone"]);?>;
                var coo=liste_coo[idx_coo].trim();
                var coo2=liste_coo[idx_coo+1].trim();
                if (coo == coo2) {
                    nb_lecteurs++;
                    continue;
                }

                size=size+(nb_lecteurs*<?PHP print ($GLOBALS["affiche_page"]["parametres"]["coef_icone"]);?>);

                var lat_long=coo.split(" ");
                if (lat_long.length == 2) {
                    var lat=lat_long[0];
                    var longi=lat_long[1];
                    
                    //alert ("place point "+lat+" "+longi+" "+size);
                    var circle = L.circle([lat, longi], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: size
                    }).addTo(mymap);
                    circle.bindPopup(nb_lecteurs+" personne(s)");
                    
                    /**
                    var myLatlng = new google.maps.LatLng(lat,longi);
                    var icone = new google.maps.MarkerImage ("<?PHP print ($GLOBALS["affiche_page"]["parametres"]["url_icone"]);?>", new google.maps.Size(size, size), new google.maps.Point(0,0), new google.maps.Point(size/2,size/2), new google.maps.Size(size, size));
                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        title: nb_lecteurs+" personnes",
                        icon: icone
                    });
                    marker.setMap(map);
                    **/
               } else {
                   // alert ("pb : "+lat_long.length+" elements");
               }
               nb_lecteurs=1;
            }
          }
        
         
         </script>
             
             
             
    </head>
    <body onload="init()">
        
        <div id="mapid"></div>
        
    </body>
       
    
</html>


