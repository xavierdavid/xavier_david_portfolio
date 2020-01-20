
class Map {
    constructor() {
        // Propriété qui initialise la carte Leaflet  
        this.mymap = L.map('mapid').setView([47.85, 2.3518], 6);

        // Propriété qui stocke les marqueurs verts utilisés pour la carte des réservations
        this.greenIcon = L.icon({ // Marqueurs verts
            iconUrl: '../img/map_marker_green.png',
            iconSize:     [35, 35], // Taille de l'icone
            iconAnchor:   [15, 35], // Point de rattachement de l'icone au marqueur de localisation
            popupAnchor:  [3, -35] // Positionnement de la fenêtre popup par rapport au point de rattachement de l'icone
        })
        // Propriété qui stocke les marqueurs  oranges utilisés pour la carte des réservation
        this.orangeIcon = L.icon({ // Marqueurs oranges
            iconUrl: '../img/map_marker_orange.png',
            iconSize:     [35, 35],
            iconAnchor:   [15, 35],
            popupAnchor:  [3, -35]
        })
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
    } 
    
    // Méthode qui récupère les données des d'expériences professionelle en base de données avec Ajax 
    // Les données sont au format JSON
    getExperienceMap() {
        // On instancie un nouvel objet AjaxRequest
        let ExperienceRequest = new AjaxRequest();
        // On récupère les données dans la base de données, au format JSON
        // L'url correspond à la route de la méthode getExperiences() du contrôleur ExperienceController
        ExperienceRequest.ajaxGet("http://127.0.0.1:8000/get/experiences", function(response){
            // On affiche les données JSON dans la console
            console.log(response);
        })
    }

}


// On créée une instance de la classe Map
let map = new Map();

// On créé la carte en appellant la méthode createMap()
map.createMap();

// On récupère les noms et adresses des expériences au format JSON en appelant la méthode getExperienceMap 
map.getExperienceMap();