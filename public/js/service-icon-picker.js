/**
 * Service Icon Auto-Suggestion — client-side resolver & picker UI
 * Rules sourced from window.ServiceIconConfig (exported from ServiceIconResolver).
 */
(function () {
    'use strict';

    if (!window.ServiceIconConfig) return;

    const config = window.ServiceIconConfig;
    const iconMap = Object.fromEntries(
        (config.icons || []).map((icon) => [icon.key, icon])
    );

    let manualOverride = false;
    let debounceTimer = null;

    const els = {};

    function cacheElements() {
        els.form = document.getElementById('serviceForm');
        if (!els.form) return false;

        els.nameInput = document.getElementById('service_name');
        els.categoryInput = document.getElementById('service_category');
        els.subcategoryInput = document.getElementById('service_subcategory');
        els.iconInput = document.getElementById('service_icon');
        els.preview = document.getElementById('serviceIconPreview');
        els.label = document.getElementById('serviceIconLabel');
        els.categoryHint = document.getElementById('serviceIconCategoryHint');
        els.alternatives = document.getElementById('serviceIconAlternatives');
        els.badge = document.getElementById('serviceIconSuggestedBadge');
        els.templates = document.getElementById('serviceIconTemplates');

        return !!(els.nameInput && els.iconInput && els.preview && els.alternatives);
    }

    function normalizeText(text) {
        return (text || '')
            .toLowerCase()
            .trim()
            .replace(/[^\p{L}\p{N}\s&\-/]+/gu, ' ')
            .replace(/\s+/g, ' ');
    }

    function scoreHaystack(haystack) {
        const scores = {};

        (config.icons || []).forEach((icon) => {
            if (icon.key === 'default') return;

            let score = 0;

            (icon.keywords || []).forEach((keyword) => {
                const normalizedKeyword = normalizeText(keyword);
                if (!normalizedKeyword) return;

                if (haystack.includes(normalizedKeyword)) {
                    score += Math.max(10, normalizedKeyword.length * 2);

                    const wordRegex = new RegExp('\\b' + normalizedKeyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'u');
                    if (wordRegex.test(haystack)) {
                        score += 5;
                    }
                }
            });

            if (score > 0) {
                scores[icon.key] = score;
            }
        });

        return scores;
    }

    function buildAlternatives(primary, ranked) {
        const alternatives = [];
        const related = iconMap[primary]?.related || [];

        related.forEach((key) => {
            if (key !== primary && !alternatives.includes(key)) {
                alternatives.push(key);
            }
        });

        ranked.forEach((key) => {
            if (key !== primary && !alternatives.includes(key)) {
                alternatives.push(key);
            }
        });

        const filtered = alternatives.filter((key) => key !== 'default' && iconMap[key]);

        if (filtered.length < 2) {
            ['haircut', 'facial', 'spa', 'makeup'].forEach((key) => {
                if (key !== primary && !filtered.includes(key) && iconMap[key]) {
                    filtered.push(key);
                }
            });
        }

        return filtered.slice(0, 4);
    }

    function resolve(name, category, subcategory) {
        const haystack = normalizeText([name, category, subcategory].filter(Boolean).join(' '));
        const scores = scoreHaystack(haystack);

        if (Object.keys(scores).length === 0) {
            const defaultKey = config.default || 'default';
            return {
                primary: defaultKey,
                alternatives: buildAlternatives(defaultKey, []),
                category: iconMap[defaultKey]?.category || null,
            };
        }

        const ranked = Object.entries(scores)
            .sort((a, b) => b[1] - a[1])
            .map(([key]) => key);

        const primary = ranked[0];

        return {
            primary,
            alternatives: buildAlternatives(primary, ranked),
            category: iconMap[primary]?.category || null,
        };
    }

    function cloneIcon(key, size) {
        if (!els.templates) return null;

        const normalizedKey = iconMap[key] ? key : (config.default || 'default');
        const template = els.templates.querySelector(`template[data-icon="${normalizedKey}"]`)
            || els.templates.querySelector('template[data-icon="default"]');

        if (!template) return null;

        const node = template.content.firstElementChild.cloneNode(true);
        node.classList.remove('svc-icon--sm', 'svc-icon--md', 'svc-icon--lg', 'svc-icon--xl');
        node.classList.add('svc-icon--' + (size || 'lg'));

        return node;
    }

    function setSelectedIcon(key, fromManual) {
        const normalized = iconMap[key] ? key : (config.default || 'default');

        els.iconInput.value = normalized;

        if (fromManual) {
            manualOverride = true;
        }

        updateBadge();
        updatePreview(normalized);
        renderAlternatives(normalized, lastSuggestion?.alternatives || []);
    }

    let lastSuggestion = null;

    function updatePreview(key) {
        const iconNode = cloneIcon(key, 'xl');
        if (!iconNode) return;

        els.preview.innerHTML = '';
        els.preview.appendChild(iconNode);

        const meta = iconMap[key] || iconMap[config.default];
        els.label.textContent = meta?.label || 'Salon Service';
    }

    function updateBadge() {
        if (!els.badge) return;

        if (manualOverride) {
            els.badge.textContent = 'Custom';
            els.badge.classList.add('is-manual');
        } else {
            els.badge.textContent = 'Suggested';
            els.badge.classList.remove('is-manual');
        }
    }

    function renderAlternatives(selectedKey, alternativeKeys) {
        els.alternatives.innerHTML = '';

        const keys = [selectedKey, ...alternativeKeys.filter((k) => k !== selectedKey)];

        keys.forEach((key) => {
            if (!iconMap[key]) return;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'svc-icon-picker__alt-btn' + (key === selectedKey ? ' is-selected' : '');
            btn.setAttribute('data-icon-key', key);
            btn.setAttribute('role', 'option');
            btn.setAttribute('aria-selected', key === selectedKey ? 'true' : 'false');

            const iconNode = cloneIcon(key, 'lg');
            if (iconNode) btn.appendChild(iconNode);

            const label = document.createElement('span');
            label.className = 'svc-icon-picker__alt-btn-label';
            label.textContent = iconMap[key].label;
            btn.appendChild(label);

            btn.addEventListener('click', () => {
                setSelectedIcon(key, true);
            });

            els.alternatives.appendChild(btn);
        });
    }

    function maybeSuggestCategory(category) {
        if (!category || !els.categoryInput) return;
        if (els.categoryInput.dataset.userEdited === 'true') return;

        const current = (els.categoryInput.value || '').trim();
        if (current === '') {
            els.categoryInput.value = category;
        }
    }

    function runSuggestion() {
        const name = els.nameInput.value || '';
        const category = els.categoryInput?.value || '';
        const subcategory = els.subcategoryInput?.value || '';

        lastSuggestion = resolve(name, category, subcategory);

        if (lastSuggestion.category) {
            els.categoryHint.textContent = 'Suggested category: ' + lastSuggestion.category;
            maybeSuggestCategory(lastSuggestion.category);
        } else {
            els.categoryHint.textContent = '';
        }

        if (!manualOverride) {
            els.iconInput.value = lastSuggestion.primary;
            updatePreview(lastSuggestion.primary);
        }

        renderAlternatives(
            els.iconInput.value || lastSuggestion.primary,
            lastSuggestion.alternatives
        );
    }

    function debouncedSuggest() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(runSuggestion, 180);
    }

    function bindEvents() {
        els.nameInput.addEventListener('input', debouncedSuggest);

        if (els.categoryInput) {
            els.categoryInput.addEventListener('input', () => {
                els.categoryInput.dataset.userEdited = 'true';
                debouncedSuggest();
            });
        }

        if (els.subcategoryInput) {
            els.subcategoryInput.addEventListener('input', debouncedSuggest);
        }
    }

    window.ServiceIconPicker = {
        initAdd() {
            if (!cacheElements()) return;

            manualOverride = false;
            els.categoryInput?.removeAttribute('data-user-edited');
            els.iconInput.value = config.default || 'default';
            updateBadge();
            runSuggestion();
        },

        initEdit(service) {
            if (!cacheElements()) return;

            manualOverride = true;
            els.categoryInput?.setAttribute('data-user-edited', 'true');

            const storedIcon = service.icon || config.default || 'default';
            els.iconInput.value = iconMap[storedIcon] ? storedIcon : (config.default || 'default');

            lastSuggestion = resolve(
                service.service_name || '',
                service.category || '',
                service.subcategory || ''
            );

            updateBadge();
            updatePreview(els.iconInput.value);
            renderAlternatives(els.iconInput.value, lastSuggestion.alternatives);
        },

        refresh: runSuggestion,
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (cacheElements()) {
            bindEvents();
        }
    });
})();
