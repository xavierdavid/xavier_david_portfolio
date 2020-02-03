
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
            /*return false;*/
        });
    });






