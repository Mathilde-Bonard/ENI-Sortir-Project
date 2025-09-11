// Variables globales
const selectVille = document.getElementById('sortie_ville');
const selectLieuVille = document.getElementById('lieu_ville');
const selectLieu = document.getElementById('sortie_lieu');
const lieuModal = document.querySelector('.modal-lieu');
const lieuForm = document.querySelector('#lieu_form');
const buttonAddLieu = document.querySelector('#addLieuButton');
const lieuDetails = document.querySelector('.lieu_details');

// Configuration API - Adapte selon ton environnement
const apiBaseUrl = '/Sortir/public/'; // Ou utilise {{ path('sortie_list') }} dans ton Twig

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initEventListeners();
});

function initEventListeners() {
    // Changement de ville -> mise à jour des lieux
    if (selectVille) {
        selectVille.addEventListener('change', function() {
            const villeId = selectVille.value;

            // Vider le select lieu et cacher les détails
            selectLieu.innerHTML = '<option value="">Choisissez un lieu...</option>';
            lieuDetails.style.display = 'none';

            if (villeId) {
                refreshLieux(villeId);
            }
        });
    }

    // Changement de lieu -> affichage des détails
    if (selectLieu) {
        selectLieu.addEventListener('change', function() {
            const lieuId = selectLieu.value;

            if (lieuId) {
                displayLieuDetails(lieuId);
            } else {
                lieuDetails.style.display = 'none';
            }
        });
    }

    // Ouverture de la modale
    if (buttonAddLieu) {
        buttonAddLieu.addEventListener('click', function() {
            openLieuModal();
        });
    }

    // Fermeture de la modale (clic à l'extérieur)
    if (lieuModal) {
        lieuModal.addEventListener('click', function(event) {
            if (event.target === lieuModal) {
                lieuModal.style.display = 'none';
            }
        });
    }

    // Soumission du formulaire de création de lieu
    if (lieuForm) {
        lieuForm.addEventListener('submit', function(event) {
            event.preventDefault();
            submitNewLieu();
        });
    }
}

// Récupérer et afficher les lieux d'une ville
function refreshLieux(villeId) {
    fetch(apiBaseUrl + 'api/lieux/' + villeId)
        .then(response => response.json())
        .then(data => {
            // Vider d'abord le select
            selectLieu.innerHTML = '<option value="">Choisissez un lieu...</option>';

            // Ajouter les lieux
            data.forEach(lieu => {
                const option = document.createElement('option');
                option.value = lieu.id;
                option.textContent = lieu.nom;
                selectLieu.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des lieux:', error);
        });
}

// Afficher les détails d'un lieu (rue, code postal)
function displayLieuDetails(lieuId) {
    fetch(apiBaseUrl + 'api/lieu/' + lieuId + '/detail')
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les détails
            const rueElement = document.querySelector('#rue');
            const cpElement = document.querySelector('#cp');

            if (rueElement) rueElement.textContent = 'Rue : ' + (data.rue || '');
            if (cpElement) cpElement.textContent = 'Code Postal : ' + (data.codePostal || '');

            // Afficher la section
            lieuDetails.style.display = 'flex';
        })
        .catch(error => {
            console.error('Erreur lors du chargement des détails:', error);
            lieuDetails.style.display = 'none';
        });
}

// Ouvrir la modale de création de lieu
function openLieuModal() {
    // Pré-sélectionner la ville actuelle
    if (selectVille.value && selectLieuVille) {
        selectLieuVille.value = selectVille.value;
    }

    // Afficher la modale
    lieuModal.style.display = 'flex';
}

// Soumettre le formulaire de création de lieu
function submitNewLieu() {
    // Récupérer les données du formulaire
    const formData = new FormData(lieuForm);
    const data = {
        nom: formData.get('lieu[nom]'),
        rue: formData.get('lieu[rue]'),
        ville: formData.get('lieu[ville]')
    };

    // Envoyer à l'API
    fetch(apiBaseUrl + 'api/lieux', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            // Fermer la modale
            lieuModal.style.display = 'none';

            // Ajouter le nouveau lieu au select
            const newOption = document.createElement('option');
            newOption.value = data.id;
            newOption.textContent = data.nom;
            selectLieu.appendChild(newOption);

            // Sélectionner le nouveau lieu
            selectLieu.value = data.id;

            // Afficher ses détails
            displayLieuDetails(data.id);

            // Vider le formulaire
            lieuForm.reset();
        })
        .catch(error => {
            console.error('Erreur lors de la création du lieu:', error);
            alert('Erreur lors de la création du lieu');
        });
}