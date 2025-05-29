// Sélectionne la barre de navigation dans l'en-tête
let navbar = document.querySelector('.header .flex .navbar');

// Sélectionne le bouton du menu (icône hamburger)
let menuBtn = document.querySelector('.header .flex #menu-btn');

// Si le bouton du menu est cliqué
menuBtn.onclick = () => {
   // Ajoute ou retire la classe 'fa-times' sur le bouton (pour changer l'icône)
   menuBtn.classList.toggle('fa-times');
   // Ajoute ou retire la classe 'active' sur la barre de navigation (pour afficher/cacher le menu)
   navbar.classList.toggle('active');
}

// Quand on fait défiler la page (scroll)
window.onscroll = () => {
   // Retire la classe 'fa-times' du bouton menu (remet l'icône hamburger normale)
   menuBtn.classList.remove('fa-times');
   // Retire la classe 'active' de la barre de navigation (cache le menu)
   navbar.classList.remove('active');
}

// Pour chaque champ input de type "number" sur la page
document.querySelectorAll('input[type="number"]').forEach(inputNumber => {
   // Quand on saisit quelque chose dans le champ
   inputNumber.oninput = () => {
      // Si la longueur de la valeur dépasse la limite maxLength
      if(inputNumber.value.length > inputNumber.maxLength) 
         // Tronque la valeur pour ne garder que les caractères jusqu'à maxLength
         inputNumber.value = inputNumber.value.slice(0, inputNumber.maxLength);
   }
});
