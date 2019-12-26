

// Identification des éléments du DOM

let slider = document.getElementById("slider");
let sliderItem = document.getElementsByClassName("slider_element");
let next = document.getElementById("arrow_right");
let prev = document.getElementById("arrow_left");
let pause = document.getElementById("pause");
let play = document.getElementById("play");


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
        this.intervalSlider
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
        //this.intervalSlider = setInterval(that.shiftSlide(1),this.timer);
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
        play.style.display = "block";
    }

    playSlider() {
        // Mise en marche du défilement automatique 

    }

}


// Création d'un nouvel objet 'slider' par instanciation 
let projectSlider = new Slider();

// Appel de la fonction d'initialisation
projectSlider.sliderInit();

// Lancement du défilement automatique du slider
projectSlider.intervalSlider = setInterval("projectSlider.shiftSlide(1)", projectSlider.timer);

// Gestion des événements

// Click events - A chaque click sur les boutons next et prev
next.addEventListener("click", function(evt){
    // On déplace le slider vers la gauche 
    // On définit la direction du déplacement dans le paramètre de la fonction shiftSlide()
    projectSlider.shiftSlide(1);
});

prev.addEventListener("click", function(evt){
    // On déplace le slider vers la droite
    projectSlider.shiftSlide(-1);
});

pause.addEventListener("click", function(evt){
    // On met le slider en pause
    projectSlider.pauseSlider();
});
    
// Transition Events - A la fin de chaque transition du slider
slider.addEventListener("transitionend", function(e){
    // On vérifie l'index à l'aide de la méthode checkIndex()
    projectSlider.checkIndex();
});