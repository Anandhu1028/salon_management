document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.querySelector('.sidebar');
    var toggleButton = document.querySelector('.sidebar-toggle');
    var overlay = document.querySelector('.sidebar-overlay');

    if (!sidebar || !toggleButton || !overlay) {
        return;
    }

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('visible');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('visible');
    }

    toggleButton.addEventListener('click', function () {
        if (sidebar.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1199) {
            sidebar.classList.remove('show');
            overlay.classList.remove('visible');
        }
    });
});
