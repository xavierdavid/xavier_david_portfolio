
// COOKIE BANNER ANIMATIONS JQUERY //

/* On récupère la valeur de la clé 'cookieSeen' dans le localStorage
/* Si la valeur de la clé 'cookieSeen' est différente de 'shown' (autrement dit si la bannière de cookie n'a pas encore été visualisée), ... */
if (sessionStorage.getItem("cookieSeen") != "shown") {
  /* ... alors on affiche la bannière avec un effet de fading */
  $('.cookie_banner').delay(2000).fadeIn('slow');
  /* On stocke la clé 'cookieSeen' avec la valeur 'shown' dans le localStorage */
  sessionStorage.setItem("cookieSeen","shown");
}

/* Suppression de la bannière de cookie au clic sur le bouton avec un effet de fading */
$('.close').click(function() {
  $('.cookie_banner').fadeOut();
})

/* On affiche dans la console la valeur de la clé 'cookieSeen' */
for (var i = 0; i < sessionStorage.length; i++) {
  console.log(sessionStorage.getItem(sessionStorage.key(i)));
}