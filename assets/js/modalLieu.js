import {refreshLieux, refreshVille} from "api";
import {Elements} from "constantsSortie";

export {openLieuModal, closeModal}
export {initModal}
export {submitLieu}


function openLieuModal() {
    const { selectVille, selectLieuVille, buttonAddLieu, lieuModal } = Elements;
    // Présélection de la ville
    if (selectVille.value) {
        refreshVille(selectVille.value);
    }
    buttonAddLieu.addEventListener('click', function () {
        selectLieuVille.value = selectVille.value;
        if (selectLieuVille.value) {
            refreshLieux(selectLieuVille.value);
        }
        lieuModal.style.display = 'flex';
    })
}
function closeModal() {
    const { lieuModal, selectLieu } = Elements;

    // Sortie de la modale quand click en dehors
    lieuModal.addEventListener('click', function (event) {
        if (event.target === lieuModal) {
            selectLieu.value = lieu.id;
            lieuModal.style.display = 'none';
        }
    });
}

// Traitement du formulaire d'un nouveau lieu dans la modale
function submitLieu(){
    const { lieuForm } = Elements;
    lieuForm.addEventListener('submit', function (event){

        event.preventDefault();
        // Récupération des données du formulaire
        const formData = new FormData(lieuForm);
        const data = {
            nom: formData.get('lieu[nom]'),
            rue: formData.get('lieu[rue]'),
            ville: formData.get('lieu[ville]'),
        };
        // Envoie les données en JSON
        fetch(apiCreateBaseUrl, {
            method: 'POST',
            headers: {
                'Content-Type':'application/json'
            },
            body: JSON.stringify(data)
            })
            .then(response=>response.json())
            // Traitement après succès
            .then(data=>{
                // Fermeture de la modale
                const lieu = document.querySelector('.modal-lieu');
                lieu.style.display = 'none';

                // Création d'une nouvelle option avec le lieu créé
                const lieuCree = document.createElement('option');
                lieuCree.value = data.id;
                lieuCree.textContent = data.nom;
                const selectLieu = document.getElementById('sortie_lieu');
                selectLieu.appendChild(lieuCree);

                selectLieu.value = data.id;

            })
        lieuForm.reset();
        // Préselection nouveau lieu
        refreshLieux();

    })
}

function initModal(){
    openLieuModal();
    closeModal();
}
