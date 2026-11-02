/* Only compose user-initiated outbound links. No tracking, tool embeds or test requests. */
(() => {
    'use strict';
    const form = document.getElementById('pico-resource-target');
    if (!form) return;
    const input = document.getElementById('pico-test-url');
    const status = document.getElementById('pico-test-advice');
    const links = [...document.querySelectorAll('[data-pico-tester]')];
    const text = window.picoResourceText;
    const bases = {pagespeed: 'https://pagespeed.web.dev/', richresults: 'https://search.google.com/test/rich-results'};
    const local = host => {
        host = host.replace(/^\[|\]$/g, '').toLowerCase();
        return !host.includes('.') && !host.includes(':') || /(^|\.)(localhost|local|test|internal|invalid)$/.test(host) || /^(127\.|10\.|192\.168\.|169\.254\.|0\.)/.test(host) || /^172\.(1[6-9]|2\d|3[01])\./.test(host) || /^(::1$|::$|f[cd][\da-f]{2}:|fe[89ab][\da-f]:)/.test(host);
    };
    function sync() {
        links.forEach(link => link.href = bases[link.dataset.picoTester]);
        try {
            const url = new URL(input.value.trim());
            if (!['http:', 'https:'].includes(url.protocol) || url.username || url.password) throw new Error('Invalid URL');
            if (local(url.hostname)) {status.textContent = text.local;return false;}
            url.hash = '';
            links.forEach(link => {
                const target = new URL(bases[link.dataset.picoTester]);
                if (link.dataset.picoTester === 'pagespeed') {target.pathname = '/analysis';target.searchParams.set('form_factor', 'mobile');}
                target.searchParams.set('url', url.href);
                link.href = target.href;
            });
            status.textContent = text.ready;
            return true;
        } catch(error) {status.textContent = text.invalid;return false;}
    }
    form.addEventListener('submit', event => {event.preventDefault();sync();});
    input.addEventListener('input', sync);
    document.getElementById('pico-copy-test-url').addEventListener('click', async () => {
        try {
            const url = new URL(input.value.trim());
            if (!['http:', 'https:'].includes(url.protocol) || url.username || url.password) throw new Error('Invalid URL');
        } catch(error) {status.textContent = text.invalid;return;}
        try {await navigator.clipboard.writeText(input.value.trim());status.textContent = text.copied;}
        catch(error) {input.focus();input.select();status.textContent = text.copy;}
    });
    sync();
})();
