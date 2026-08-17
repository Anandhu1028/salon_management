

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
const multiSelectInstances = new WeakMap();

function createMultiSelect(select) {
    if (multiSelectInstances.has(select)) return multiSelectInstances.get(select);

    const wrapper = document.createElement('div');
    wrapper.className = 'nice-select nice-select--multi';

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'nice-select__trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');

    const icon = select.dataset.icon || 'bi-list-check';
    const iconMarkup = `<span class="form-field-icon"><i class="bi ${icon}" aria-hidden="true"></i></span>`;

    trigger.innerHTML = `
        <div class="nice-select__left">
            ${iconMarkup}
            <div class="nice-select__tags"></div>
            <span class="nice-select__placeholder"></span>
        </div>
        <div class="nice-select__right">
            <span class="nice-select__count-badge" style="display:none;"></span>
            <i class="bi bi-chevron-down" aria-hidden="true"></i>
        </div>
    `;

    const panel = document.createElement('div');
    panel.className = 'nice-select__panel nice-select__panel--multi';
    panel.innerHTML = `
        <div class="nice-select__search">
            <i class="bi bi-search"></i>
            <input type="search" placeholder="Search..." aria-label="Search">
        </div>
        <div class="nice-select__multi-actions">
            <div class="d-flex gap-2">
                <button type="button" class="btn-multi-action btn-select-all">Select All</button>
                <button type="button" class="btn-multi-action btn-clear-all">Clear</button>
            </div>
            <span class="multi-selected-info">0 selected</span>
        </div>
        <div class="nice-select__options" role="listbox"></div>
    `;

    select.classList.add('nice-select__native');
    select.after(wrapper);
    wrapper.append(trigger, panel);

    const search = panel.querySelector('input');
    const optionsList = panel.querySelector('.nice-select__options');
    const tagsContainer = trigger.querySelector('.nice-select__tags');
    const placeholder = trigger.querySelector('.nice-select__placeholder');
    const countBadge = trigger.querySelector('.nice-select__count-badge');
    const infoSpan = panel.querySelector('.multi-selected-info');
    const selectAllBtn = panel.querySelector('.btn-select-all');
    const clearAllBtn = panel.querySelector('.btn-clear-all');

    const close = () => {
        wrapper.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        search.value = '';
        optionsList.querySelectorAll('.nice-select__option').forEach(item => { item.hidden = false; });
    };

    const toggleOption = (val) => {
        const option = [...select.options].find(o => String(o.value) === String(val));
        if (option) {
            option.selected = !option.selected;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            render();
        }
    };

    const selectAll = () => {
        const query = search.value.toLowerCase().trim();
        [...select.options].forEach(option => {
            if (!option.value) return;
            if (!query || option.textContent.toLowerCase().includes(query)) {
                option.selected = true;
            }
        });
        select.dispatchEvent(new Event('change', { bubbles: true }));
        render();
    };

    const clearAll = () => {
        const query = search.value.toLowerCase().trim();
        [...select.options].forEach(option => {
            if (!option.value) return;
            if (!query || option.textContent.toLowerCase().includes(query)) {
                option.selected = false;
            }
        });
        select.dispatchEvent(new Event('change', { bubbles: true }));
        render();
    };

    selectAllBtn.addEventListener('click', (e) => { e.stopPropagation(); selectAll(); });
    clearAllBtn.addEventListener('click', (e) => { e.stopPropagation(); clearAll(); });

    const render = () => {
        const selectedOptions = [...select.options].filter(o => o.value && o.selected);
        const count = selectedOptions.length;

        trigger.disabled = select.disabled;
        trigger.setAttribute('aria-expanded', wrapper.classList.contains('is-open') ? 'true' : 'false');

        // Update tags
        tagsContainer.innerHTML = '';
        if (count > 0) {
            placeholder.style.display = 'none';
            tagsContainer.style.display = 'flex';

            const displayLimit = 2;
            selectedOptions.slice(0, displayLimit).forEach(opt => {
                const tag = document.createElement('span');
                tag.className = 'nice-select__tag';
                const label = opt.textContent.trim().split(' — ')[0];
                tag.innerHTML = `<span>${label}</span><i class="bi bi-x" data-value="${opt.value}"></i>`;
                tag.querySelector('.bi-x').addEventListener('click', (e) => {
                    e.stopPropagation();
                    opt.selected = false;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    render();
                });
                tagsContainer.append(tag);
            });

            if (count > displayLimit) {
                const moreTag = document.createElement('span');
                moreTag.className = 'nice-select__tag nice-select__tag--more';
                moreTag.textContent = `+${count - displayLimit} more`;
                tagsContainer.append(moreTag);
            }

            countBadge.style.display = 'inline-flex';
            countBadge.textContent = count;
        } else {
            placeholder.style.display = 'inline';
            placeholder.textContent = select.dataset.placeholder || 'Select options';
            tagsContainer.style.display = 'none';
            countBadge.style.display = 'none';
        }

        infoSpan.textContent = `${count} selected`;

        // Render options list
        optionsList.innerHTML = '';
        [...select.options].forEach(option => {
            if (!option.value) return;
            const item = document.createElement('button');
            item.type = 'button';
            item.className = `nice-select__option nice-select__option--multi ${option.selected ? 'is-selected' : ''}`;
            item.dataset.value = option.value;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(option.selected));

            const isChecked = option.selected;
            const checkboxHtml = `<span class="nice-select__checkbox ${isChecked ? 'is-checked' : ''}"><i class="bi bi-check" aria-hidden="true"></i></span>`;

            const rawText = option.textContent.trim();
            const parts = rawText.split(' — ');
            const mainText = parts[0];
            const subText = parts[1] || '';

            item.innerHTML = `
                ${checkboxHtml}
                <div class="nice-select__option-text">
                    <span class="nice-select__option-title">${mainText}</span>
                    ${subText ? `<span class="nice-select__option-subtitle">${subText}</span>` : ''}
                </div>
            `;

            item.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleOption(option.value);
            });

            optionsList.append(item);
        });
    };

    trigger.addEventListener('click', () => {
        if (select.disabled) return;
        const opening = !wrapper.classList.contains('is-open');
        document.querySelectorAll('.nice-select.is-open').forEach(item => item.classList.remove('is-open'));
        wrapper.classList.toggle('is-open', opening);
        trigger.setAttribute('aria-expanded', String(opening));
        if (opening) search.focus();
    });

    search.addEventListener('input', () => {
        const query = search.value.toLowerCase().trim();
        optionsList.querySelectorAll('.nice-select__option').forEach(item => {
            item.hidden = !item.textContent.toLowerCase().includes(query);
        });
    });

    select.addEventListener('change', render);
    new MutationObserver(render).observe(select, { childList: true, subtree: true, attributes: true });
    render();

    const instance = {
        render,
        close,
        setValues: (vals) => {
            const valArray = Array.isArray(vals) ? vals.map(String) : (vals ? [String(vals)] : []);
            [...select.options].forEach(o => {
                o.selected = valArray.includes(String(o.value));
            });
            select.dispatchEvent(new Event('change', { bubbles: true }));
            render();
        }
    };
    multiSelectInstances.set(select, instance);
    return instance;
}

window.createMultiSelect = createMultiSelect;
window.refreshMultiSelect = (select) => createMultiSelect(select).render();
window.setMultiSelectValues = (select, values) => createMultiSelect(select).setValues(values);

function createNiceSelect(select) {
    if (select.multiple) {
        return createMultiSelect(select);
    }

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

window.refreshNiceSelect = (select) => {
    if (!select) return;
    if (select.multiple) {
        return createMultiSelect(select).render();
    }
    return createNiceSelect(select).render();
};

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
