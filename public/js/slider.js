
// Identification des éléments du DOM

let slider = document.getElementById("slider");
let sliderItem = document.getElementsByClassName("slider_element");
let next = document.getElementById("arrow_right");
let prev = document.getElementById("arrow_left");
let pause = document.getElementById("pause");
let play = document.getElementById("play");
let moreLink = document.getElementsByClassName("more_link");
let lessLink = document.getElementsByClassName("less_link");
let projectView = document.getElementsByClassName("project_view");
let sliderControll = document.querySelector("div.slider_controll");



// Création d'une classe 'Slider'

class Slider {
    constructor(itemCounter, itemSize, totalItem, timer) {
        this.itemCounter = 0, // Compteur par défaut à 0 (premier élément)
        this.itemSize = slider.offsetWidth, // Largeur des éléments du slider
        this.totalItem = sliderItem.length, // Nombre d'éléments du slider
        this.firstItem = sliderItem[0], // Premier élément du slider
        this.lastItem = sliderItem[sliderItem.length - 1], // Dernier élément du slider
        this.timer = 5000, // Défilement automatique toutes les 3 secondes
        this.allowShift = true, // Autoriser le décalage pour passer à l'item suivant ou précédent
        this.posX1 = 0,
        this.posX2 = 0,
        this.posInitial, 
        this.posFinal,
        this.threshold = 100,
        this.index = 0, // Index initial
        this.intervalSlider,
        this.nomGlobal // Variable globale
    }

    sliderInit() {
        // Méthode permettant d'initialiser le slider 

        // Paramétrage d'un slider infini
        let cloneFirstItem = this.firstItem.cloneNode(true); // Clonage du premier élément du slider
        let cloneLastItem = this.lastItem.cloneNode(true); // Clonage du dernier élément du slider
        // Insertion du clone du premier élément à la fin du slider (après le dernier élément)
        slider.appendChild(cloneFirstItem);
        // Insertion du clone du dernier élément au début du slider (avant le premier élément)
        slider.insertBefore(cloneLastItem, this.firstItem);
        // Lancement du défilement automatique
        this.intervalSlider = setInterval(this.shiftSlide.bind(this,1),this.timer);

        // Gestion des évènements

        // Mise en pause du slider au clic sur le bouton 'pause'
        pause.addEventListener("click", this.pauseSlider.bind(this));
        // Mise en lecture du slider au clic sur le bouton 'play'
        play.addEventListener("click", this.playSlider.bind(this));
        // Déplacement vers l'élément suivant au clic sur le bouton 'next'
        next.addEventListener("click", this.shiftSlide.bind(this,1));
        // Déplacement vers l'élément précédent au clic sur le bouton 'prev'
        prev.addEventListener("click", this.shiftSlide.bind(this,-1));
        // Vérification de l'index à la fin de chaque transition du slider
        slider.addEventListener("transitionend", this.checkIndex.bind(this)); 
    }       
    
    shiftSlide(dir){
        // Méthode permettant de déplacer le slider vers la gauche ou vers la droite

        // On affecte une propriété de transition à l'élément #slider
        slider.style.transition = "all 0.7s";
        
        // Ajout de la classe 'shifting' à l'élément #slider pour activer la transition css
        slider.classList.add("shifting");
        // Si le déplacement du slider est possible
        if(this.allowShift){
            // On définit l'origine le point de départ du déplacement de l'élément #slider 
            // Il est défini ici sur le coin supérieur gauche de l'élément slider
            // Position du coin supérieur gauche de l'élément slider
            this.posInitial = slider.offsetLeft;

            // On teste les directions (1 vers la gauche et -1 vers la droite)
            if (dir == 1){
                // On définit la valeur de la propriété 'left' en pixels
                slider.style.left = (this.posInitial - this.itemSize) + "px";
                // On incrémente l'index 
                this.index++;

            } else if (dir == -1){
                slider.style.left = (this.posInitial + this.itemSize) + "px";
                this.index--;
            }
        }
        this.allowShift = false;
    }

    checkIndex(){
        // Après chaque transition, on supprime la classe 'shifting' à l'élément #slider pour désactiver la transition css
        slider.classList.remove("shifting");

        // On teste ensuite la valeur de l'index 
        
        // Si l'on se trouve sur le clone du dernier élément (au début du slider)
        if (this.index == -1) {
            // Alors on redéfinit la valeur de la propriété 'left' pour se repositionner sur le dernier élément du slider
            slider.style.left = -(this.totalItem * this.itemSize) + "px";
            // On désactive la propriété de transition de l'élément #slider pour masquer l'effet de défilement lors du repositionnement
            slider.style.transition = "none";
            // On redéfinit l'index sur le dernier élément
            this.index = this.totalItem -1;
        // Sinon, si l'on se trouve sur le clone du premier élément (à la fin du slider)
        } else if (this.index == this.totalItem){
            // On rédéfinit la propriété 'left'
            slider.style.left = -(1 * this.itemSize) + "px";
            // On désactive la propriété de transition
            slider.style.transition = "none";
            // On redéfinit l'index sur le premier élément
            this.index = 0;
        }
        this.allowShift = true;
    }

    pauseSlider() {
        // Arrêt du défilement automatique
        clearInterval(this.intervalSlider);
        // On masque le bouton 'pause'
        pause.style.display = "none";
        // On affiche le bouton 'play'
        play.style.display = "block";
    }

    playSlider() {
        // Mise en marche du défilement automatique 
        this.intervalSlider = setInterval(this.shiftSlide.bind(this,1),this.timer);
        // On masque le bouton 'play'
        play.style.display = "none";
        // On affiche le bouton 'pause' 
        pause.style.display = "block";
    }

}




// Création d'un nouvel objet 'slider' par instanciation 
let projectSlider = new Slider();

// Appel de la fonction d'initialisation du slider
projectSlider.sliderInit();


// Gestion du slider avec les flèches du clavier 
/*document.addEventListener("keydown", function(evt){
    if(evt.which === 39){ // Flèche droite du clavier
        projectSlider.shiftSlide(1);
    }
    if(evt.which === 37){ // Flèche gauche du clavier
        projectSlider.shiftSlide(-1);
    }
});*/


// Affichage dynamique de la <div> de contenu des projets 
// La méthode 'getElementsByClassName'retourne un type HTMLCollection
// On parcours tous les éléments (d'indice i) de cette collection pour récupérer chaque élément moreLink et ProjectView
for(let i=0; i <moreLink.length; i++) {
    moreLink[i].addEventListener("click", function(evt) {
        // On annule le comportement par défaut du lien 
        evt.preventDefault();

        // On stoppe le défilement du slider 
        projectSlider.pauseSlider();

        // On masque la barre de contrôle du slider
        sliderControll.style.display = "none";

        // On masque l'élément 'moreLink' 
        moreLink[i].style.display = "none";
        // On affiche l'élément 'lessLink'
        lessLink[i].style.display = "block";

        // Affichage de la <div> (ayant l'indice i) de contenu des projets du slider
        // Modification du style d'affichage avec la propriété 'display: block;'
        projectView[i].style.display = "block";
    })
}

// Fermeture dynamique de la <div> de contenu des projets 
for(let i=0; i <lessLink.length; i++) {
    lessLink[i].addEventListener("click", function(evt) {
        // On annule le comportement par défaut du lien 
        evt.preventDefault();

        // On affiche la barre de contrôle du slider
        sliderControll.style.display = "flex";

        // On lance le défilement du slider 
        projectSlider.playSlider();

        // On masque l'élément 'lessLink' 
        lessLink[i].style.display = "none";
        // On affiche l'élément 'moreLink'
        moreLink[i].style.display = "block";

        // On masque la <div> (ayant l'indice i) de contenu des projets du slider
        // Modification du style d'affichage avec la propriété 'display: none;'
        projectView[i].style.display = "none";
    })
}





