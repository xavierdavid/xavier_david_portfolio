// Identification des éléments du DOM

let slider = document.getElementById("slider");
let sliderItem = document.getElementsByClassName("slider_element");
let next = document.getElementById("arrow_left");
let prev = document.getElementById("arrow_right");


// Création d'une classe 'Slider'

class Slider {
    constructor(itemCounter, itemSize, totalItem, timer) {
        this.itemCounter = 0, // Compteur par défaut à 0 (premier élément)
        this.itemSize = slider.clientWidth, // Largeur des éléments du slider
        this.totalItem = sliderItem.length, // Nombre d'éléments du slider
        this.firstItem = sliderItem[0], // Premier élément du slider
        this.lastItem = sliderItem[sliderItem.length - 1], // Dernier élément du slider
        //this.cloneFirstItem = slider[0].cloneNode('true'), // Clonage du premier élément du slider
        //this.cloneLastItem = slider[sliderItem.length - 1].cloneNode('true'), // Clonage du dernier élément du slider
        this.timer = 5000 // Défilement automatique toutes les 5 secondes
    }

    sliderInit() {
        let cloneFirstItem = this.firstItem.cloneNode(true); // Clonage du premier élément du slider
        let cloneLastItem = this.lastItem.cloneNode(true); // Clonage du dernier élément du slider
        
        // Insertion du clone du premier élément à la fin du slider (après le dernier élément)
        slider.appendChild(cloneFirstItem);
        // Insertion du clone du dernier élément au début du slider (avant le premier élément)
        slider.insertBefore(cloneLastItem, this.firstItem);
    }

}


// Création d'un nouvel objet slider par instanciation 
let projectSlider = new Slider();

// Appel de la fonction d'initialisation
projectSlider.sliderInit();