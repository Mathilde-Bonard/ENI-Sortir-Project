import {initModal} from "modalLieu";
import {initEventListeners} from "lieu-villeSelect";
import {initElements} from "constantsSortie";


document.addEventListener('DOMContentLoaded', function() {
    initElements();
    initEventListeners();
    // Charger la modale seulement si le bouton existe
    if (document.querySelector('#addLieuButton')) {
        initModal();
    }
});