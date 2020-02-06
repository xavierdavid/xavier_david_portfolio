
// SCROLL ANIMATIONS JQUERY //


// Scroll vers les ancres de la page à partir des liens avec Jquery
    $(document).ready(function(){
        // On déclenche l'execution d'une fonction lors du clic sur les ancres de type <a href="#"></a>
        $('a[href^="#"]').on('click',function (e) {
            // On annule le comportement par défaut
            e.preventDefault();
            // On identifie l'ancre ciblée
            var target = this.hash,
            $target = $(target);
            // On applique l'animation de scroll
            $('html, body').animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function () {
                window.location.hash = target;
            });
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
    
    



