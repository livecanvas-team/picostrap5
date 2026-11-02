/* global picostrapFontLoadingLabels */
(function(api) {
    'use strict';
    api.bind('ready', function() {
        const preview = document.getElementById('pico-font-header-preview');
        const source = document.getElementById('pico-wordpress-font-source');
        const status = document.getElementById('pico-font-header-status');
        if (!preview || !source || !status) return;
        const labels = picostrapFontLoadingLabels;
        let wordpressCode = source.value.trim();
        let stale = false;
        function render() {
            const themeCode = String(api('picostrap_fonts_header_code').get() || '').trim();
            const disableValue = api('picostrap_fonts_header_code_disable').get();
            const disabled = Boolean(disableValue) && disableValue !== '0';
            preview.value = [disabled ? '' : themeCode, wordpressCode].filter(Boolean).join('\n\n');
            let message;
            if (disabled) message = wordpressCode ? labels.disabled : labels.disabledEmpty;
            else if (themeCode && wordpressCode) message = labels.mixed;
            else message = wordpressCode ? labels.local : (themeCode ? labels.theme : labels.empty);
            status.textContent = stale ? labels.stale : message;
        }
        window.picostrapFontHeaderPreview = {
            setWordPressCode(code) {
                wordpressCode = String(code || '').trim();
                source.value = wordpressCode;
                stale = false;
                render();
            },
            markStale() { stale = true; render(); }
        };
        api('picostrap_fonts_header_code').bind(render);
        api('picostrap_fonts_header_code_disable').bind(render);
        render();
    });
})(wp.customize);
