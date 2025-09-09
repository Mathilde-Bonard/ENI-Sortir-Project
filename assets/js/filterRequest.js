document.addEventListener('DOMContentLoaded', () => {

    const RADIO_BTN = document.querySelectorAll('.radio-btn');
    if (RADIO_BTN.length === 0) return;

    
    RADIO_BTN[0].classList.add('active');

    RADIO_BTN.forEach(el => {
        el.addEventListener('click', () => {
            RADIO_BTN.forEach(el => el.classList.remove('active'));
            el.classList.add('active');
        })
    })

    const $FILTER_FORM = document.getElementById('filterForm');
    const $LIST_CONTAINER = document.getElementById('listContainer');

    $FILTER_FORM.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData($FILTER_FORM);
        const params = new URLSearchParams(formData);

        // Requête AJAX
        const url = $FILTER_FORM.getAttribute('action') + '?' + params.toString();
        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                $LIST_CONTAINER.innerHTML = html;


            })
            .catch(err => console.error('Erreur Ajax:', err));
    });
});