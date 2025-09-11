export {refreshVille, refreshLieux};
import {Elements} from 'constantsSortie';


// Appel API pour renouvellement du select lieux
function refreshLieux(villeId) {
    const { selectLieu } = Elements;
    fetch(apiBaseUrl + 'api/lieux/' + villeId)
        .then(response => response.json())
        .then(data => {
            // Affichage du nom du lieu dans le select
            for (const lieu of data) {
                const option = document.createElement('option');
                option.value = lieu.id;
                option.textContent = lieu.nom;
                selectLieu.appendChild(option);
            }
        });
}
// Appel API pour récupérer la ville sélectionnée
function refreshVille(villeId) {
    const { selectLieuVille } = Elements;
    fetch(apiBaseUrl + 'api/ville/' + villeId)
        .then(response=>response.json())
        .then(data => {
            selectLieuVille.value = data.id
        })
}