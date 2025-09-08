document.addEventListener('turbo:load', () => {

    const RADIO_BTN = document.querySelectorAll('.radio-btn');

    RADIO_BTN[0].classList.add('active');

    RADIO_BTN.forEach(el => {
        el.addEventListener('click', () => {
            RADIO_BTN.forEach(el => el.classList.remove('active'));
            el.classList.add('active');
        })
    })

    const $FILTER_FORM = document.getElementById('filterForm');
    const $LIST_CONTAINER = document.getElementById('listContainer');
    const $FILTER_CONTAINER = document.getElementById('filterContainer');

    $FILTER_FORM.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData($FILTER_FORM);
        const params = new URLSearchParams(formData);

        // Affiche les filtres actifs
        // $FILTER_CONTAINER.innerHTML = '';
        // params.forEach((value, key) => {
        //     if (value) {
        //         const cleanKey = key.replace(/^filter\[(.*)]$/, '$1');
        //         const filter = `<div class="bg-slate-800/20 px-3 pt-2 pb-1.5 rounded-full text-xs text-neutral-700">${cleanKey}: ${value}</div>`;
        //         $FILTER_CONTAINER.innerHTML += filter;
        //     }
        // });

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