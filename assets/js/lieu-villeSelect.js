import {Elements} from "constantsSortie";
import {refreshLieux} from "api";
import {submitLieu} from 'modalLieu'

export {updateSelectLieu}
export {initEventListeners}

// A la selection d'une ville
function updateSelectLieu() {
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

function initEventListeners() {
    updateSelectLieu();   // Écouteur changement sélection ville pour mettre à jour lieux
    submitLieu();        // Soumission du formulaire nouveau lieu dans la modale
}