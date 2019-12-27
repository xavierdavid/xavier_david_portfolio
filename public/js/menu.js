// Transfert du contenu du menu 'hamburger-content' dans la sidebar 'hamburger-sidebar-body' // 

// Identification des éléments du DOM 

let content = document.getElementById('hamburger-content');
let sidebarBody = document.getElementById('hamburger-sidebar-body');
let button = document.getElementById('hamburger-button');
let overlay = document.getElementById('hamburger-overlay');
let sidebar = document.getElementById('hamburger-sidebar');



class Menu {
    
    menuInit() {
        sidebarBody.innerHTML = content.innerHTML; // On affecte le contenu du content à sidebarBody

        // Gestionnaire d'évènement 

        // Déclenchement du menu sidebar et de l'overlay au clic sur le bouton 'hamburger'
        button.addEventListener('click', function(evt){
            evt.preventDefault(); // Annulation du comportement par défaut
            overlay.classList.add('overlay-activated'); // Affectation de la classe 'overlay-activated' à l'élément 'overlay' pour activer l'overlay 
            sidebar.classList.add('sidebar-activated'); // Affectation de la classe 'sidebar-activated' à l'élément 'sidebar' pour déclencher l'animation
        });

        // Suppression du menu sidebar et de l'overlay au clic sur 'overlay'
        overlay.addEventListener('click', function(evt){
            evt.preventDefault(); // Annulation du comportement par défaut
            overlay.classList.remove('overlay-activated'); // On supprime la classe 'overlay-activated' à l'élément 'overlay' pour désactiver l'overlay
            sidebar.classList.remove('sidebar-activated'); // On supprime la classe 'sidebar-activated' à l'élément 'sidebar' pour désactiver la sidebar
        });

        // Suppression du menu sidebar et de l'overlay au clic sur la touche 'échap'
        document.addEventListener('keydown', function(evt){
            
            if (overlay.classList.contains('overlay-activated') && sidebar.classList.contains('sidebar-activated')){
                // Gestion d'un appui'long' sur la touche 'echap'
                if (evt.repeat === false && evt.which === 27){
                    overlay.classList.remove('overlay-activated'); // On supprime la classe 'overlay-activated' à l'élément 'overlay' pour désactiver l'overlay
                    sidebar.classList.remove('sidebar-activated'); // On supprime la classe 'sidebar-activated' à l'élément 'sidebar' pour désactiver la sidebar
                }   
            }  
        });
    }
}

let menuDisplay = new Menu();

menuDisplay.menuInit();