document.addEventListener("DOMContentLoaded", () => {
    let $collapse = document.querySelector('.sortie__collapse');
    let $backdrop = document.querySelector('.collapse_backdrop');
    let $toggleBtn = document.querySelectorAll('.toggle__collapse')

    $toggleBtn.forEach(btn => {
        btn.addEventListener('click', () => {
            if($collapse.classList.contains('active')) {
                document.body.style.overflowY = "unset";
                $backdrop.classList.add('opacity-0')
                $collapse.classList.remove("active");
            } else {
                document.body.style.overflowY = "hidden";
                $backdrop.classList.remove('opacity-0')
                $collapse.classList.add("active");
            }
        })
    })
})