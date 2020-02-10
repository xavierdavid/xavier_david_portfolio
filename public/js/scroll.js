
// SCROLL ANIMATIONS JQUERY //


// Scroll vers les ancres de la page à partir des liens avec Jquery
    $(document).ready(function(){
        // On déclenche l'execution d'une fonction lors du clic sur un lien vers les ancres de type <a href="#home_experiences"></a>
        $('a[href^="#"]').on('click',function (e) {
            // On annule le comportement par défaut
            e.preventDefault();
            // On récupère la partie '#' de l'url de l'ancre actuellement ciblée (retourne par exemple '#home_experiences')
            let anchorHash = this.hash;
            // On identifie l'élément ciblé du DOM vers lequel s'appliquera l'animation du scroll
            $target = $(anchorHash); // (Ex: $(#home_experiences))
            // On récupère la position de l'ancre actuellement ciblée
            let anchorY = $target.offset().top;
            // On applique l'animation de scroll pour déclencher le défilement jusqu'à l'ancre
            $('html,body').animate({ scrollTop: anchorY - 90 }, 1000); // On décale de 90 pixels l'affichage pour ne pas coller le bord haut de l'affichage du navigateur et on défile en 1 seconde jusqu'à l'ancre.
        });
    });



// Scroll vers le haut avec effet de 'fade' sur le bouton de retour vers le haut lors du scroll avec Jquery
    $(document).ready(function() {
        // On affiche ou on masque progessivement le bouton de retour vers le haut lors du scroll
        $(window).scroll(function(){
            // Dès lors que le scroll vers le bas dépasse les 350px (css par défaut 'display: none') ... 
            if ($(this).scrollTop() > 350) {
                // ... on affiche progressivement le bouton de retour vers le haut. 
                $('.scrollButton_up').fadeIn();
            }
            else
            {
                // On masque progessivement le bouton
                $('.scrollButton_up').fadeOut();
            }
        });
        
        // On déclenche le retour vers le haut de l'écran lors du clic sur le bouton
        $('.scrollButton_up').click(function(){
            // Le retour vers le haut se fera en douceur, réparti sur 0,8 seconde, soit 800 millisecondes
            $('html, body').animate({scrollTop :0},800);
        });
    });



// Déclenchement de l'animation des éléments education_header et map_header de la page d'accueil lors du scroll 
    $(window).on('scroll', function() {
 
        // On récupère la position du scroll en cours sur la page
        let scrollY = $(window).scrollTop();

        // On récupère les éléments devant être animés 
        let educationHeader = $('#education_header');
        let mapHeader = $('#map_header');

        // Récupération de la position des éléments à animer sur la page courante 
        let educationHeaderPosY = educationHeader.offset().top;
        let mapHeaderPosY = mapHeader.offset().top;
        
        // Ajustement de la position du scroll en ajoutant la hauteur de la barre de navigation qui masque l'affichage
        let ajustScrollY = scrollY + 500;

        /*console.log('position actuelle du scroll : ' + scrollY);
        console.log('position de education_header : ' + educationHeaderPosY);
        console.log('position ajustée du scroll : ' + ajustScrollY);
        console.log('position de map_header : ' + mapHeaderPosY);*/
        
        // si l'élément educationHeaderPosY est visible dans la page
        if(ajustScrollY > educationHeaderPosY) {
            // On modifie le style css de l'élément pour déclencher l'animation @keyframes définie en css
            educationHeader.css('animation','goToRight 1s');
            educationHeader.css('animation-fill-mode', 'forwards'); // l'animation conserve son état final
        }

        // si l'élément mapHeaderPosY est visible dans la page
        if(ajustScrollY > mapHeaderPosY) {
            // On modifie le style css de l'élément pour déclencher l'animation  
            mapHeader.css('animation','goToRight 1s');
            mapHeader.css('animation-fill-mode', 'forwards');
        }   
    });

    

// Déclenchement de l'animation de l'élément experiences_header de la page d'accueil lors du scroll 
    $(window).on('scroll', function() {
 
        // On récupère la position du scroll en cours sur la page
        let scrollY = $(window).scrollTop();

        // On récupère l'élément devant être animé 
        let experiencesHeader = $('#experiences_header');
      
        // Récupération de la position de l'élément à animer sur la page courante 
        let experiencesHeaderPosY = experiencesHeader.offset().top;
        
        // Ajustement de la position du scroll en ajoutant la hauteur de la barre de navigation qui masque l'affichage
        let ajustScrollY = scrollY + 500;

        // si l'élément educationHeaderPosY est visible dans la page
        if(ajustScrollY > experiencesHeaderPosY) {
            // On modifie le style css de l'élément pour déclencher l'animation @keyframes définie en css
            experiencesHeader.css('animation','goToLeft 1s');
            experiencesHeader.css('animation-fill-mode', 'forwards'); // l'animation conserve son état final
        }

    });



    /*$("a[href^='#']").click(function (e) {
        let 
        yPos,
        yInitPos,
        target = ($($(this).attr("href") + ":first"));
        
        // On annule le comportement initial au cas ou la base soit différente de la page courante.
        e.preventDefault();
        
        // On récupère la position du scroll en cours sur la page
        yInitPos = $(window).scrollTop();
        
        // On ajoute le hash dans l'url.
        window.location.hash = $(this).attr("href");
        
        // Comme il est possible que l'ajout du hash perturbe le défilement, on va forcer le scrollTop à son endroit inital.
        $(window).scrollTop(yInitPos);
        
        // On cible manuellement l'ancre pour en extraire sa position.
        // Si c'est un ID on l'obtient.
        target = ($($(this).attr("href") + ":first"));
         
        // Sinon on cherche l'ancre dans le name d'un a.
        if (target.length == 0) {
        target = ($("a[name=" + $(this).attr("href").replace(/#/gi,"") + "]:first"))
        }
        
        // Si on a trouvé un name ou un id, on défile.
        if (target.length == 1) {
        yPos = target.offset().top; // Position de l'ancre.
        
        // On anime le défilement jusqu'à l'ancre.
        $('html,body').animate({ scrollTop: yPos - 90 }, 1000); // On décale de 90 pixels l'affichage pour ne pas coller le bord haut de l'affichage du navigateur et on défile en 1 seconde jusqu'à l'ancre.
        }
        });*/
    
    



