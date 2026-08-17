

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.showToast = (message, type = 'success') => {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.append(container);
    }

    const icons = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
    const toast = document.createElement('div');
    toast.className = 'premium-toast';
    toast.innerHTML = `<div class="toast-icon ${type}"><i class="bi ${icons[type] || icons.success}" aria-hidden="true"></i></div><div class="toast-text"></div>`;
    toast.querySelector('.toast-text').textContent = message;
    container.append(toast);

    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};

const niceSelectInstances = new WeakMap();

function createNiceSelect(select) {
    if (niceSelectInstances.has(select)) return niceSelectInstances.get(select);

    const wrapper = document.createElement('div');
    wrapper.className = 'nice-select';
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'nice-select__trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');

    const icon = select.dataset.icon;
    const hasDot = select.dataset.dot !== undefined;
    let iconMarkup = '';
    if (hasDot) {
        iconMarkup = '<span class="form-field-icon form-field-icon--dot"><span class="status-dot"></span></span>';
    } else if (icon) {
        iconMarkup = `<span class="form-field-icon"><i class="bi ${icon}" aria-hidden="true"></i></span>`;
    }

    trigger.innerHTML = `<div class="nice-select__left">${iconMarkup}<span class="nice-select__value"></span></div><i class="bi bi-chevron-down" aria-hidden="true"></i>`;

    const panel = document.createElement('div');
    panel.className = 'nice-select__panel';
    panel.innerHTML = '<div class="nice-select__search"><i class="bi bi-search"></i><input type="search" placeholder="Search options..." aria-label="Search options"></div><div class="nice-select__options" role="listbox"></div>';

    select.classList.add('nice-select__native');
    select.after(wrapper);
    wrapper.append(trigger, panel);

    const search = panel.querySelector('input');
    const optionsList = panel.querySelector('.nice-select__options');
    const value = trigger.querySelector('.nice-select__value');
    const close = () => {
        wrapper.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        search.value = '';
        optionsList.querySelectorAll('.nice-select__option').forEach(item => { item.hidden = false; });
    };
    const render = () => {
        const selected = select.options[select.selectedIndex];
        value.textContent = selected?.textContent.trim() || select.dataset.placeholder || 'Select an option';
        trigger.disabled = select.disabled;
        trigger.setAttribute('aria-expanded', wrapper.classList.contains('is-open') ? 'true' : 'false');
        optionsList.innerHTML = '';
        [...select.options].forEach(option => {
            if (!option.value) return;
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'nice-select__option';
            item.dataset.value = option.value;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(option.selected));
            item.innerHTML = `<span class="nice-select__option-copy">${option.textContent.trim()}</span>${option.selected ? '<i class="bi bi-check-circle-fill" aria-hidden="true"></i>' : ''}`;
            item.addEventListener('click', () => {
                select.value = option.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                close();
            });
            optionsList.append(item);
        });
        panel.classList.toggle('nice-select__panel--searchable', select.options.length > 6);
    };

    trigger.addEventListener('click', () => {
        if (select.disabled) return;
        const opening = !wrapper.classList.contains('is-open');
        document.querySelectorAll('.nice-select.is-open').forEach(item => item.classList.remove('is-open'));
        wrapper.classList.toggle('is-open', opening);
        trigger.setAttribute('aria-expanded', String(opening));
        if (opening && panel.classList.contains('nice-select__panel--searchable')) search.focus();
    });
    search.addEventListener('input', () => {
        const query = search.value.toLowerCase().trim();
        optionsList.querySelectorAll('.nice-select__option').forEach(item => { item.hidden = !item.textContent.toLowerCase().includes(query); });
    });
    select.addEventListener('change', render);
    new MutationObserver(render).observe(select, { childList: true, subtree: true, attributes: true });
    render();

    const instance = { render, close };
    niceSelectInstances.set(select, instance);
    return instance;
}

window.refreshNiceSelect = (select) => createNiceSelect(select).render();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert.alert-success').forEach(alert => {
        const message = alert.cloneNode(true);
        message.querySelector('.btn-close')?.remove();
        window.showToast(message.textContent.trim(), 'success');
        alert.remove();
    });

    document.querySelectorAll('.premium-modal .form-select:not(#service_id)').forEach(createNiceSelect);
    document.addEventListener('shown.bs.modal', (event) => {
        event.target.querySelectorAll('.form-select:not(#service_id)').forEach(select => window.refreshNiceSelect(select));
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.nice-select')) {
            document.querySelectorAll('.nice-select.is-open').forEach(item => item.classList.remove('is-open'));
        }
    });

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
