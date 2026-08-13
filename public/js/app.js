document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
    const toggleButton = document.getElementById('sidebarToggle') || document.querySelector('.sidebar-toggle');
    const overlay = document.getElementById('sidebarOverlay') || document.querySelector('.sidebar-overlay');
    const appShell = document.querySelector('.app-shell');

    if (sidebar && window.matchMedia('(min-width: 1025px)').matches) {
        sidebar.addEventListener('mouseenter', () => {
            appShell?.classList.add('sidebar-expanded');
        });
        sidebar.addEventListener('mouseleave', () => {
            if (!sidebar.classList.contains('is-pinned')) {
                appShell?.classList.remove('sidebar-expanded');
            }
        });
    }

    if (sidebar && toggleButton && overlay) {
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
            if (window.innerWidth > 1024) {
                closeSidebar();
            }
        });
    }
});

/**
 * Global Toast Notification Helper
 * @param {string} message - Toast message
 * @param {string} type - 'success', 'danger', 'warning', 'info'
 */
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'premium-toast';

    const icons = {
        success: 'bi-check-circle-fill',
        danger: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };

    toast.innerHTML = `
        <div class="toast-icon ${type}">
            <i class="bi ${icons[type] || icons.success}"></i>
        </div>
        <div class="toast-text">${message}</div>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3500);
}

/**
 * Utility HTML escape function
 */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
