function subscription() {
    document.addEventListener('click', e => {
        if(e.target.classList.contains('sub_btn')) {
            const btn = e.target;

            fetch(btn.dataset.url, {
                method: "POST",
                      }).then(response => response.text()).then(html => {
                btn.closest('.sortie_listener').innerHTML = html;
            })
        }
    })
}
document.addEventListener('DOMContentLoaded', () => {
    subscription();
})