
class Map {
    constructor() {
        // Propriété qui initialise la carte Leaflet (coordonnées et zoom initial) 
        this.mymap = L.map('mapid').setView([47.85, 2.3518], 6);

        // Propriété qui stocke les marqueurs verts utilisés pour la carte des réservations
        this.myIcon = L.icon({ // Marqueurs verts
            iconUrl: '../img/map-marker.png',
            iconSize:     [35, 35], // Taille de l'icone
            iconAnchor:   [15, 35], // Point de rattachement de l'icone au marqueur de localisation
            popupAnchor:  [3, -35] // Positionnement de la fenêtre popup par rapport au point de rattachement de l'icone
        });
        // Propriété qui stocke les marqueurs  oranges utilisés pour la carte des réservation
        this.orangeIcon = L.icon({ // Marqueurs oranges
            iconUrl: '../img/map_marker_orange.png',
            iconSize:     [35, 35],
            iconAnchor:   [15, 35],
            popupAnchor:  [3, -35]
        });        
    }


    // Méthode qui créée une nouvelle carte pour la réservation à l'aide l'API Leaflet
    createMap() {
        // On charge les tuiles et on les ajoute à la carte
        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            minZoom: 1,
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' + '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' + 'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox.streets',
            accessToken: 'your.mapbox.access.token',
        }).addTo(this.mymap); 

        // On appelle la méthode getExperienceMap pour récupérer les infos d'expérience ... 
        // ... et les afficher sur la carte
        this.getExperienceOnMap();  
    } 

    
    // Méthode qui récupère les données des d'expériences professionelle en base de données avec Ajax 
    // Les données sont au format JSON
    getExperienceOnMap() {

        // On récupère la carte
        let myExperienceMap = this.mymap;

        // On récupère les icônes
        let icon = this.myIcon;

        // On instancie un nouvel objet AjaxRequest
        let experienceRequest = new AjaxRequest();
        // On récupère les données dans la base de données, au format JSON en utilisant la méthode ajaxGet()
        // L'url correspond à la route de la méthode getExperiences() du contrôleur ExperienceController
        experienceRequest.ajaxGet("http://127.0.0.1:8000/get/experiences", function(response){
            // On transforme la réponse JSON en un tableau d'objets Javascript 'MyResponse'
            let MyResponse = JSON.parse(response);

            // On extrait de la réponse, les informations concernant les expériences
            let experiencesInfos = MyResponse.experiences;
        
            // On affiche les informations concernant les expériences dans la console
            console.log(experiencesInfos);

            // Pour chaque expérience ...
            for(let i=0; i<experiencesInfos.length; i++){
                
                // On récupère le nom de la structure
                let experienceCompany = experiencesInfos[i].company;
                // On récupère l'adresse du lieu d'expérience 
                let experienceAddress = experiencesInfos[i].address;
                // On récupère l'intitulé de la mission
                let experienceMission = experiencesInfos[i].mission;
                
                // On affiche les informations de l'expérience dans la console
                console.log("Le nom de l'entreprise est : " + experienceCompany);
                console.log("L'adresse du lieu d'expérience est : " + experienceAddress);
                console.log("L'intitulé de la mission est : " + experienceMission);              
                
                // On instancie un nouvel objet AjaxRequest
                let geolocalisationRequest = new AjaxRequest();

                // Récupération des données : longitude et latiude de l'adresse complète au format JSON, 1 résultat uniquement
                geolocalisationRequest.ajaxGet("https://nominatim.openstreetmap.org/search?q='"+experienceAddress+"'&format=json&addressdetails=1&limit=1&polygon_svg=1", function(reponse){
                    
                    // On transforme la réponse JSON en un tableau d'objets Javascript
                    let addressInfos = JSON.parse(reponse);
                    
                    // On parcourt tous les objets adressInfos ...
                    addressInfos.forEach(addressInfo => {
                
                        // On affiche les données JSON dans la console  
                        console.log(addressInfos);
                        // On affiche la longitude dans la console
                        console.log("Longitude de l'adresse = " + addressInfo.lon);
                        // On affiche la latitude dans la console
                        console.log("Latitude de l'adresse = " + addressInfo.lat);

                        // On ajoute les marqueurs sur la carte
                        //let marker = L.marker([adressInfo.lat, adressInfo.lon],{icon: this.}).addTo(this.mymap);
                        let marker = L.marker([addressInfo.lat, addressInfo.lon], {icon: icon}).addTo(myExperienceMap);
                        // On indique enfin, sous forme de popup les informations concernant l'expérience à l'aide de la méthode bindPopup de l'API Leaflet...
                        marker.bindPopup("<b>Structure</b><br>" + experienceCompany + "<br><b>Mission</b><br>" + experienceMission);
                   })
                })
            }
        })
    }



    // Méthode qui récupère la longitude et la latitude d'une adresse en AJAX avec le service Nominatim API 
    /*getGeolocalisation(experienceCompany, experienceAddress,experienceMission){

        // On récupère la carte
        let mymap = this.mymap;
        
        // On instancie un nouvel objet AjaxRequest
        let geolocalisationRequest = new AjaxRequest();

        // Récupération des données : longitude et latiude de l'adresse complète au format JSON, 1 résultat uniquement
        geolocalisationRequest.ajaxGet("https://nominatim.openstreetmap.org/search?q='"+ experienceAdress +"'&format=json&addressdetails=1&limit=1&polygon_svg=1", function(response){
            
            // On transforme la réponse JSON en un tableau d'objets Javascript
            let addressInfos = JSON.parse(response);
            
            // On parcourt tous les objets adressInfos ...
            addressInfos.forEach(addressInfo => {
        
                // On affiche les données JSON dans la console  
                console.log(addressInfo);
                // On affiche la longitude dans la console
                console.log("Longitude de l'adresse = " + addressInfo.lon);
                // On affiche la latitude dans la console
                console.log("Latitude de l'adresse = " + addressInfo.lat);
                
                // On ajoute les marqueurs sur la carte
                //let marker = L.marker([adressInfo.lat, adressInfo.lon],{icon: this.greenIcon}).addTo(this.mymap);
                let marker = L.marker([addressInfo.lat, addressInfo.lon]).addTo(mymap);  
                //return [addressInfo.lon, addressInfo.lat]   
            })
            
        })
    }*/
}


// On créée une instance de la classe Map
let map = new Map();

// On créé la carte en appellant la méthode createMap()
map.createMap();

