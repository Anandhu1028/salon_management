/* ============================================================
   SHARED 3-DOT ACTION POPOVER (JS)
   ============================================================
   Same behaviour as the Job Cards page: click the three-dot
   button to open a small popover with action buttons. Reused on
   Attendance / Customers / Services / Marketing (and any other
   premium-list page using the .pli-action-menu-wrap markup).
   ============================================================ */

(function () {
    if (window.togglePliActions) {
        // Already loaded on this page — avoid double-binding listeners.
        return;
    }

    function closeWrap(wrapper) {
        wrapper.classList.remove('is-open');

        const button = wrapper.querySelector('.pli-action-dots');
        if (button) {
            button.classList.remove('is-open');
            button.setAttribute('aria-expanded', 'false');
        }

        const row = wrapper.closest('.premium-list-item');
        if (row) {
            row.classList.remove('action-menu-row-open');
        }
    }

    window.togglePliActions = function (button) {
        const wrapper = button.closest('.pli-action-menu-wrap');
        if (!wrapper) return;

        const currentRow = button.closest('.premium-list-item');

        // Close all other open menus
        document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(menu => {
            if (menu !== wrapper) {
                closeWrap(menu);
            }
        });

        const isOpen = wrapper.classList.toggle('is-open');

        button.classList.toggle('is-open', isOpen);
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (currentRow) {
            currentRow.classList.toggle('action-menu-row-open', isOpen);
        }
    };

    window.closePliActions = function (element) {
        const wrapper = element.closest('.pli-action-menu-wrap');
        if (!wrapper) return;
        closeWrap(wrapper);
    };

    // Close when clicking outside
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.pli-action-menu-wrap')) {
            document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(closeWrap);
        }
    });

    // Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('.pli-action-menu-wrap.is-open').forEach(closeWrap);
    });
})();
