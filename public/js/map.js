
class Map {
    constructor() {
        // Propriété qui initialise la carte Leaflet (coordonnées et zoom initial) 
        this.mymap = L.map('mapid',{scrollWheelZoom:false}).setView([47.85, 2.3518], 6);

        // Propriété qui stocke les marqueurs utilisés pour les expériences professionnalles
        this.myExperienceIcon = L.icon({ 
            iconUrl: '../img/experience-icon.png',
            iconSize:     [40, 40], // Taille de l'icone
            iconAnchor:   [15, 35], // Point de rattachement de l'icone au marqueur de localisation
            popupAnchor:  [3, -35] // Positionnement de la fenêtre popup par rapport au point de rattachement de l'icone
        });
        // Propriété qui stocke les marqueurs utilisés pour les formations professionnelles
        this.myEducationIcon = L.icon({ 
            iconUrl: '../img/education-icon.png',
            iconSize:     [40, 40],
            iconAnchor:   [15, 35],
            popupAnchor:  [3, -35]
        });        
    }


    // Méthode qui créée une nouvelle carte pour la réservation à l'aide l'API Leaflet
    createMap() {
        // On charge les tuiles et on les ajoute à la carte
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoieGF2ZGF2MjgiLCJhIjoiY2tsY2xodzg5MHFybjJvcDBvY2cxbDlnbyJ9.sfM7hfI6aGQJaIJhBnjEOA', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'your.mapbox.access.token'
        }).addTo(this.mymap);

        // On appelle la méthode getExperienceOnMap pour récupérer les infos des expériences ... 
        // ... et les afficher sur la carte
        this.getExperienceOnMap();

        // On appelle la méthode getEducationOnMap pour récupérer les infos des formations ... 
        // ... et les afficher sur la carte
        this.getEducationOnMap();
    } 

    
    // Méthode qui récupère les données des expériences professionelles en base de données avec Ajax 
    // Les données sont au format JSON
    getExperienceOnMap() {

        // On récupère la carte
        let myExperienceMap = this.mymap;

        // On récupère les icônes
        let icon = this.myExperienceIcon;

        // On instancie un nouvel objet AjaxRequest
        let experienceRequest = new AjaxRequest();
        // On récupère les données dans la base de données, au format JSON en utilisant la méthode ajaxGet()
        // L'url correspond à la route de la méthode getExperiences() du contrôleur ExperienceController
        experienceRequest.ajaxGet("/get/experiences", function(response){
 
            // On transforme la réponse JSON en un tableau d'objets Javascript 'MyResponse'
            let MyResponse = JSON.parse(response);

            // On extrait de la réponse, les informations concernant les expériences
            let experiencesInfos = MyResponse.experiences;

            // Pour chaque expérience ...
            for(let i=0; i<experiencesInfos.length; i++){
                
                // On récupère le nom de la structure
                let experienceCompany = experiencesInfos[i].company;
                // On récupère l'adresse du lieu d'expérience 
                let experienceAddress = experiencesInfos[i].address;
                // On récupère l'intitulé de la mission
                let experienceMission = experiencesInfos[i].mission;            
                
                // On instancie un nouvel objet AjaxRequest
                let geolocalisationRequest = new AjaxRequest();

                // Récupération des données : longitude et latiude de l'adresse complète au format JSON, 1 résultat uniquement
                geolocalisationRequest.ajaxGet("https://nominatim.openstreetmap.org/search?q='"+experienceAddress+"'&format=json&addressdetails=1&limit=1&polygon_svg=1", function(reponse){
                    
                    // On transforme la réponse JSON en un tableau d'objets Javascript
                    let addressInfos = JSON.parse(reponse);
                    
                    // On parcourt tous les objets adressInfos ...
                    addressInfos.forEach(addressInfo => {
                        // On ajoute les marqueurs sur la carte
                        let marker = L.marker([addressInfo.lat, addressInfo.lon], {icon: icon}).addTo(myExperienceMap);
                        // On indique enfin, sous forme de popup les informations concernant l'expérience à l'aide de la méthode bindPopup de l'API Leaflet...
                        marker.bindPopup("<b>Structure</b><br>" + experienceCompany + "<br><b>Mission</b><br>" + experienceMission);
                   })
                })
            }
        })
    }


    // Méthode qui récupère les données des formations professionelles en base de données avec Ajax 
    // Les données sont au format JSON
    getEducationOnMap() {

        // On récupère la carte
        let myEducationMap = this.mymap;

        // On récupère les icônes
        let icon = this.myEducationIcon;

        // On instancie un nouvel objet AjaxRequest
        let educationRequest = new AjaxRequest();
        // On récupère les données dans la base de données, au format JSON en utilisant la méthode ajaxGet()
        // L'url relative utilisée correspond à la route de la méthode getEducations() du contrôleur EducationController
        //educationRequest.ajaxGet("http://127.0.0.1:8000/get/educations", function(response){
        educationRequest.ajaxGet("/get/educations", function(response){
              
            // On transforme la réponse JSON en un tableau d'objets Javascript 'MyResponse'
            let MyResponse = JSON.parse(response);

            // On extrait de la réponse, les informations concernant les formations
            let educationsInfos = MyResponse.educations;
        
            // On affiche les informations concernant les formations dans la console
            //console.log(educationsInfos);

            // Pour chaque formation ...
            for(let i=0; i<educationsInfos.length; i++){
                
                // On récupère le nom de l'établissement
                let educationSchool = educationsInfos[i].school;
                // On récupère l'adresse de l'établissement 
                let educationAddress = educationsInfos[i].address;
                // On récupère l'intitulé de la certification
                let educationCertification = educationsInfos[i].certification;
                
                // On affiche les informations de la formation dans la console
                /*console.log("Le nom de l'établissement est : " + educationSchool);
                console.log("L'adresse de l'établissement est : " + educationAddress);
                console.log("L'intitulé de la certification est : " + educationCertification);*/              
                
                // On instancie un nouvel objet AjaxRequest
                let geolocalisationRequest = new AjaxRequest();

                // Récupération des données : longitude et latiude de l'adresse complète au format JSON, 1 résultat uniquement
                geolocalisationRequest.ajaxGet("https://nominatim.openstreetmap.org/search?q='"+educationAddress+"'&format=json&addressdetails=1&limit=1&polygon_svg=1", function(reponse){
                    
                    // On transforme la réponse JSON en un tableau d'objets Javascript
                    let addressInfos = JSON.parse(reponse);
                    
                    // On parcourt tous les objets adressInfos ...
                    addressInfos.forEach(addressInfo => {
                
                        // On affiche les données JSON dans la console  
                        /*console.log(addressInfos);
                        // On affiche la longitude dans la console
                        console.log("Longitude de l'adresse = " + addressInfo.lon);
                        // On affiche la latitude dans la console
                        console.log("Latitude de l'adresse = " + addressInfo.lat);*/

                        // On ajoute les marqueurs sur la carte
                        let marker = L.marker([addressInfo.lat, addressInfo.lon], {icon: icon}).addTo(myEducationMap);
                        // On indique enfin, sous forme de popup les informations concernant l'expérience à l'aide de la méthode bindPopup de l'API Leaflet...
                        marker.bindPopup("<b>Etablissement</b><br>" + educationSchool + "<br><b>Certification</b><br>" + educationCertification);
                   })
                })
            }
        })
    }
}


// On créée une instance de la classe Map
let map = new Map();

// On créé la carte en appellant la méthode createMap()
map.createMap();