export {initElements, Elements}

const Elements = {
    selectVille: null,
    selectLieuVille: null,
    selectLieu: null,
    lieuModal: null,
    lieuForm: null,
    buttonAddLieu: null,
}

function initElements () {
    Elements.selectVille = document.getElementById('sortie_ville');
    Elements.selectLieuVille = document.getElementById('lieu_ville');
    Elements.selectLieu = document.getElementById('sortie_lieu');
    Elements.lieuModal = document.querySelector('.modal-lieu');
    Elements.lieuForm = document.querySelector('#lieu_form');
    Elements.buttonAddLieu = document.querySelector('#addLieuButton');
}