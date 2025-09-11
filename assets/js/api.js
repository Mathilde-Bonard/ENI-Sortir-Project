export {refreshVille, refreshLieux, displayRueCp};
import {Elements} from 'constantsSortie';


// Appel API pour renouvellement du select lieux
function refreshLieux(villeId) {
    const { selectLieu } = Elements;
    fetch(apiBaseUrl + 'api/lieux/' + villeId)
        .then(response => response.json())
        .then(data => {
            console.log(data)
            // Affichage du nom du lieu dans le select
            for (const lieu of data) {
                const option = document.createElement('option');

                console.log(lieu.id)
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

// Appel API pour récupérer la rue et le CP

function displayRueCp(lieuId) {
    fetch(apiBaseUrl + 'api/lieu/' + lieuId +'/detail')
        .then(response => response.json())
        .then(data => {
            document.querySelector('#rue').textContent = `Rue : ${data.rue}`;
            document.querySelector('#cp').textContent = `Code Postal : ${data.codePostal}`;
            document.querySelector('.lieu_details').style.display = 'flex';
        });
}
