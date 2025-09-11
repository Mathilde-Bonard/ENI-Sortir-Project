function cancel() {
    let $cancel_btns = document.querySelectorAll('.cancel_btn');
    let $container = document.getElementById('cancel_container');

    $cancel_btns.forEach(btn => {
        btn.addEventListener('click', () => {
            let url = btn.dataset.url + '?sortie=' + btn.dataset.sortie;

            fetch(url)
                .then(res => res.text())
                .then(html => {
                    $container.innerHTML = html;
                })
                .catch(err => console.error('Erreur AJAX :', err));
        });
    });
}

document.addEventListener('DOMContentLoaded', cancel);
