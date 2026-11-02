/* Progressive enhancement for Picostrap's options screen only. */
(() => {
    'use strict';
    const root = document.getElementById('pico-options');
    if (!root) return;
    const config = window.picoOptions;
    if (!config) return;
    // Native top-layer popovers stay clear of clipped panels and narrow viewports.
    root.querySelectorAll('.pico-help-button').forEach(button => {
        const bubble = document.getElementById(button.getAttribute('popovertarget'));
        const place = () => {
            const rect = button.getBoundingClientRect();
            const width = bubble.getBoundingClientRect().width;
            const height = bubble.getBoundingClientRect().height;
            bubble.style.left = `${Math.max(12, Math.min(rect.left + rect.width / 2 - width / 2, window.innerWidth - width - 12))}px`;
            const below = rect.bottom + 10;
            bubble.style.top = `${Math.max(12, below + height <= window.innerHeight - 12 ? below : rect.top - height - 10)}px`;
        };
        if (typeof bubble.showPopover === 'function') {
            bubble.addEventListener('toggle', event => { if (event.newState === 'open') place(); });
            window.addEventListener('resize', () => { if (bubble.matches(':popover-open')) place(); });
            document.addEventListener('scroll', event => {
                if (!bubble.contains(event.target) && bubble.matches(':popover-open')) bubble.hidePopover();
            }, true);
        } else {
            // Older browsers get the same explanation inline, with an accessible toggle.
            bubble.dataset.fallback = 'true';
            bubble.hidden = true;
            button.setAttribute('aria-expanded', 'false');
            button.addEventListener('click', () => {
                bubble.hidden = !bubble.hidden;
                button.setAttribute('aria-expanded', String(!bubble.hidden));
            });
        }
    });

    const deliveryButton = document.getElementById('pico-check-delivery');
    let checkingDelivery = false;
    const checkDelivery = async () => {
        if (checkingDelivery) return;
        checkingDelivery = true;
        deliveryButton.disabled = true;
        deliveryButton.setAttribute('aria-busy', 'true');
        const summary = document.getElementById('pico-delivery-summary');
        const advice = document.getElementById('pico-delivery-advice');
        summary.textContent = config.delivery.checking;
        advice.textContent = config.delivery.checking;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 15000);
        try {
            const encodings = await Promise.all(config.deliveryTargets.map(async target => {
                const field = document.getElementById(`pico-encoding-${target.id}`);
                field.textContent = config.delivery.checking;
                try {
                    const url = new URL(target.url, location.href);
                    // Cross-origin CORS policies can hide Content-Encoding: never infer "none".
                    if (url.origin !== location.origin) throw new Error('Cross-origin headers cannot be verified');
                    const response = await fetch(url.href, {credentials: 'omit', cache: 'no-store', signal: controller.signal});
                    const type = (response.headers.get('content-type') || '').toLowerCase();
                    if (!response.ok || response.redirected || !type.includes(target.type)) {
                        await response.body?.cancel();
                        throw new Error('Unexpected resource response');
                    }
                    const encoding = (response.headers.get('content-encoding') || '').trim().toLowerCase();
                    await response.body?.cancel();
                    const labels = {gzip: 'Gzip', br: 'Brotli (br)', zstd: 'Zstandard (zstd)', deflate: 'Deflate'};
                    field.textContent = encoding && encoding !== 'identity' ? (labels[encoding] || encoding) : config.delivery.none;
                    field.dataset.encoding = encoding && encoding !== 'identity' ? 'compressed' : 'none';
                    return encoding === 'identity' ? '' : encoding;
                } catch {
                    field.textContent = config.delivery.unknown;
                    field.dataset.encoding = 'unknown';
                    return null;
                }
            }));
            const failed = encodings.includes(null);
            const missing = encodings.includes('');
            const modern = encodings.some(value => value && /\b(br|zstd)\b/.test(value));
            summary.textContent = failed ? config.delivery.failed : missing ? config.delivery.partial : config.delivery.complete;
            const suggestions = [];
            if (missing) suggestions.push(config.delivery.missing);
            if (modern) suggestions.push(config.delivery.modern);
            else if (encodings.some(value => value && /\bgzip\b/.test(value))) suggestions.push(config.delivery.gzip);
            else if (encodings.some(Boolean)) suggestions.push(config.delivery.other);
            if (failed) suggestions.push(config.delivery.failed);
            advice.textContent = suggestions.join(' ');
            const secure = document.getElementById('pico-delivery-https');
            secure.hidden = location.protocol === 'https:';
            secure.textContent = secure.hidden ? '' : config.delivery.insecure;
        } finally {
            clearTimeout(timeout);
            checkingDelivery = false;
            deliveryButton.disabled = false;
            deliveryButton.removeAttribute('aria-busy');
            deliveryButton.textContent = config.delivery.check;
        }
    };
    deliveryButton.addEventListener('click', checkDelivery);
    checkDelivery();

    const nav = root.querySelector('.pico-tabs');
    const tabs = [...nav.querySelectorAll('a')];
    const panels = [...root.querySelectorAll('.pico-tabpanel')];
    nav.setAttribute('role', 'tablist');
    tabs.forEach((tab, index) => {
        tab.id = `pico-tab-${index}`;
        tab.setAttribute('role', 'tab');
        tab.setAttribute('aria-controls', panels[index].id);
        panels[index].setAttribute('role', 'tabpanel');
        panels[index].setAttribute('aria-labelledby', tab.id);
        panels[index].tabIndex = 0;
    });
    const activate = (id, focus = false) => {
        const current = panels.findIndex(panel => panel.id === id);
        if (current < 0) return;
        tabs.forEach((tab, index) => {
            tab.setAttribute('aria-selected', String(index === current));
            tab.tabIndex = index === current ? 0 : -1;
            panels[index].hidden = index !== current;
        });
        tabs[current].scrollIntoView?.({block: 'nearest', inline: 'nearest'});
        if (focus) tabs[current].focus();
    };
    const navigate = (id, focus = false) => {
        activate(id, focus);
        history.replaceState(null, '', `#${id}`);
    };
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', event => {
            event.preventDefault();
            navigate(panels[index].id);
        });
        tab.addEventListener('keydown', event => {
            const steps = {ArrowRight: 1, ArrowLeft: -1};
            let next;
            if (event.key in steps) next = (index + steps[event.key] + tabs.length) % tabs.length;
            else if (event.key === 'Home') next = 0;
            else if (event.key === 'End') next = tabs.length - 1;
            if (next === undefined) return;
            event.preventDefault();
            navigate(panels[next].id, true);
        });
    });
    root.querySelectorAll('[data-pico-link]').forEach(link => link.addEventListener('click', event => {
        event.preventDefault();
        navigate(link.hash.slice(1));
        panels.find(panel => panel.id === link.hash.slice(1))?.focus();
    }));
    activate(panels.some(panel => `#${panel.id}` === location.hash) ? location.hash.slice(1) : panels[0].id);
    window.addEventListener('hashchange', () => activate(location.hash.slice(1)));

    const form = document.getElementById('pico-extras-form');
    const switches = [...form.querySelectorAll('input[type="checkbox"]')];
    const initial = switches.map(input => input.checked);
    const save = document.getElementById('pico-save');
    const state = document.getElementById('pico-save-state');
    const bar = form.querySelector('.pico-savebar');
    let dirty = false;
    let submitting = false;
    const updateDirty = () => {
        dirty = switches.some((input, index) => input.checked !== initial[index]);
        state.textContent = dirty ? config.unsaved : config.saved;
        bar.dataset.dirty = String(dirty);
        save.disabled = !dirty;
    };
    switches.forEach(input => input.addEventListener('change', updateDirty));
    updateDirty();
    form.addEventListener('submit', () => {
        submitting = true;
        save.disabled = true;
        save.setAttribute('aria-busy', 'true');
        state.textContent = config.saving;
    });
    window.addEventListener('beforeunload', event => {
        if (!dirty || submitting) return;
        event.preventDefault();
        event.returnValue = config.leave;
    });

    const analyze = document.getElementById('pico-analyze');
    const status = document.getElementById('pico-lab-status');
    const result = document.getElementById('pico-lab-result');
    const format = bytes => `${new Intl.NumberFormat(document.documentElement.lang || undefined, {maximumFractionDigits: 1}).format(bytes / 1024)} KB`;
    let running = false;
    analyze.addEventListener('click', async () => {
        if (running) return;
        running = true;
        analyze.disabled = true;
        analyze.setAttribute('aria-busy', 'true');
        analyze.textContent = config.analyzing;
        status.textContent = config.analyzing;
        status.dataset.error = 'false';
        result.hidden = true;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 25000);
        try {
            const home = new URL(config.home, location.href);
            if (home.origin !== location.origin) throw new Error(config.error);
            const response = await fetch(home.href, {credentials: 'omit', cache: 'no-store', signal: controller.signal});
            if (!response.ok || !response.headers.get('content-type')?.includes('text/html')) throw new Error(config.error);
            const html = await response.text();
            if (new Blob([html]).size > 4 * 1024 * 1024) throw new Error(config.error);
            const body = new URLSearchParams({action: 'picostrap_css_preview', nonce: config.nonce, html});
            const preview = await fetch(config.ajax, {method: 'POST', credentials: 'same-origin', body, signal: controller.signal});
            const payload = await preview.json();
            if (!payload.success) throw new Error(payload.data?.message || config.error);
            const data = payload.data;
            document.getElementById('pico-saved-bytes').textContent = `${format(data.saved)} / ${data.percent}%`;
            document.getElementById('pico-original-bytes').textContent = format(data.original);
            document.getElementById('pico-candidate-bytes').textContent = format(data.candidate);
            document.getElementById('pico-original-gzip').textContent = data.original_gzip === null ? config.delivery.unavailable : format(data.original_gzip);
            document.getElementById('pico-candidate-gzip').textContent = data.candidate_gzip === null ? config.delivery.unavailable : format(data.candidate_gzip);
            document.getElementById('pico-candidate-bar').style.width = `${100 - data.percent}%`;
            result.hidden = false;
            status.textContent = config.previewDone;
        } catch (error) {
            status.textContent = error.name === 'AbortError' ? config.error : (error.message || config.error);
            status.dataset.error = 'true';
        } finally {
            clearTimeout(timeout);
            running = false;
            analyze.disabled = false;
            analyze.removeAttribute('aria-busy');
            analyze.textContent = config.analyze;
        }
    });
})();
