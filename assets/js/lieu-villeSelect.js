import {Elements} from "constantsSortie";
import {refreshLieux, displayRueCp} from "api";
import {submitLieu} from 'modalLieu'


// A la selection d'une ville
function updateSelectLieu() {
    console.log("updateSelectLieu Start !")

    const { selectVille, selectLieu } = Elements;
    selectVille.addEventListener('change', function () {
        const villeId = selectVille.value;
        console.log(villeId)

        // Vider le select lieu
        selectLieu.innerHTML = '<option value="">Choisissez un lieu...</option>';

        if (villeId) { // Si une ville est sélectionnée
            refreshLieux(villeId)
        }
    });
}
function displayLieuDetails() {

    console.log("displayLieuDetails Start !")

    Elements.selectLieu.addEventListener('change', function () {
        if (this.value) {
            Elements.lieuDetails.style.display = 'flex';
            displayRueCp(this.value);
        } else {
            Elements.lieuDetails.style.display = 'none';
        }
    });
}

export function initEventListeners() {
    updateSelectLieu();   // Écoute changement sélection ville pour mettre à jour lieux
    submitLieu();        // Soumission du formulaire nouveau lieu dans la modale
    displayLieuDetails()
}