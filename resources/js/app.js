

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebarToggle');
    const overlay = document.querySelector('.sidebar-overlay');

    if (!sidebar || !toggleButton || !overlay) return;
    const closeMobileSidebar = () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('visible');
    };

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('visible');
    });

    overlay.addEventListener('click', closeMobileSidebar);
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) closeMobileSidebar();
    });
});
