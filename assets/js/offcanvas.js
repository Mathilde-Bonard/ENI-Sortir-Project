function initOffcanvas() {
    document.addEventListener("click", function(e) {
        if (e.target.closest('.toggle__collapse')) {
            let target = e.target.closest('.toggle__collapse').dataset.target;

            console.log(target)

            let $collapse = document.getElementById(target);
            let $backdrop = document.getElementById('collapse_backdrop');

            console.log($collapse)

            if ($collapse.classList.contains('active')) {
                document.body.style.overflowY = "unset";
                $backdrop.classList.remove('active');
                $collapse.classList.remove("active");
            } else {
                document.body.style.overflowY = "hidden";
                $backdrop.classList.add('active');
                $collapse.classList.add("active");
            }
        } else {
            let $collapse = document.querySelector('.custom__collapse.active');
            let $backdrop = document.querySelector('.collapse_backdrop');
            let $collapseCancel = document.querySelector('.collapse__cancel');

            console.log(e.target)

            if (e.target === $collapseCancel) {
                document.body.style.overflowY = "unset";
                $backdrop.classList.remove('active');
                $collapse.classList.remove("active");
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", initOffcanvas)
