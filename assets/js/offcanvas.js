function initOffcanvas() {
    document.addEventListener("click", function(e) {
        if (e.target.closest('.toggle__collapse')) {
            let target = e.target.closest('.toggle__collapse').dataset.target;

            let $collapse = document.getElementById(target);
            let $backdrop = document.querySelector('.collapse_backdrop');

            if ($collapse.classList.contains('active')) {
                document.body.style.overflowY = "unset";
                $backdrop.classList.add('opacity-0');
                $collapse.classList.remove("active");
            } else {
                document.body.style.overflowY = "hidden";
                $backdrop.classList.remove('opacity-0');
                $collapse.classList.add("active");
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", initOffcanvas)
