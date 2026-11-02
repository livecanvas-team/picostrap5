/* global picostrapWordPressFontData */
(function($, api) {
    'use strict';
    const data = picostrapWordPressFontData;
    let fonts = data.fonts;
    let importing = false;
    const roles = {
        body: { family: 'SCSSvar_font-family-base', picker: 'fontpickerbasefont' },
        headings: { family: 'SCSSvar_headings-font-family', picker: 'fontpickerheadingsfont' }
    };

    api.bind('ready', function() {
        // The existing picker elements are created by customizer.js on DOM ready.
        $(document).ready(function() {
            const pickers = {};
            const updates = {};
            async function refreshFonts() {
                try {
                    const body = new URLSearchParams({ action: 'picostrap_wordpress_fonts', nonce: data.nonce });
                    const response = await fetch(data.ajaxUrl, { method: 'POST', credentials: 'same-origin', body });
                    const result = await response.json();
                    if (!response.ok || !result.success || !result.data?.fonts || typeof result.data.headerCode !== 'string') throw new Error(data.error);
                    fonts = result.data.fonts;
                    window.picostrapFontHeaderPreview?.setWordPressCode(result.data.headerCode);
                    Object.values(updates).forEach(update => update());
                } catch (error) {
                    window.picostrapFontHeaderPreview?.markStale();
                    throw error;
                }
            }
            Object.entries(roles).forEach(function([role, config]) {
                const picker = document.getElementById(config.picker);
                if (!picker) return;
                pickers[role] = picker;
                const status = $('<p class="pico-font-source-status" role="status"></p>');
                api.control(config.family).container.append(status);
                const suggestion = $('<div class="pico-font-hosting-offer" hidden><p></p><button type="button" class="button">Host locally</button></div>');
                api.control(config.family).container.append(suggestion);
                let remoteFont = null;
                let importMessage = '';
                function currentRemoteFont() {
                    if (api('picostrap_wp_font_' + role).get()) return null;
                    try {
                        const font = JSON.parse(api(role + '_font_object').get() || '{}');
                        const family = api(config.family).get().replaceAll('"', '').replaceAll("'", '').split(',')[0].trim();
                        return font.id && font.family === family ? font : null;
                    } catch (error) { return null; }
                }
                const update = function() {
                    const slug = api('picostrap_wp_font_' + role).get();
                    status.text(importMessage || (slug ? (fonts[slug] ? data.hostedLabel + fonts[slug].name : data.missing) : ''));
                    remoteFont = currentRemoteFont();
                    suggestion.prop('hidden', !remoteFont || !data.canImport);
                    suggestion.find('p').text(remoteFont ? remoteFont.family + ' loads from a CDN. Host its Latin, normal-style weights on your site with one click. Files are saved to WordPress immediately; Publish applies the font choice.' : '');
                    suggestion.find('button').prop('disabled', importing);
                    picker.setHostingOffer?.(data.canImport ? remoteFont : null, importing, importMessage);
                    picker.setHostedFonts(fonts, { manageUrl: data.manageUrl, libraryUrl: data.libraryUrl, empty: data.empty, error: data.error, selected: slug });
                };
                updates[role] = update;
                api('picostrap_wp_font_' + role).bind(update);
                api(role + '_font_object').bind(update);
                api(config.family).bind(function() { importMessage = ''; setTimeout(update, 0); });
                async function hostLocally() {
                    if (importing || !remoteFont) return;
                    const selected = remoteFont;
                    const selectedFamily = api(config.family).get();
                    importing = true;
                    importMessage = 'Preparing local font hosting…';
                    Object.values(updates).forEach(update => update());
                    try {
                        const slug = await window.picostrapHostFontLocally(selected, function(message) {
                            importMessage = message;
                            update();
                        });
                        await refreshFonts();
                        if (!fonts[slug]) throw new Error('The files were imported, but the hosted list could not be refreshed. Open the Font Library or try Refresh list.');
                        if (currentRemoteFont()?.id === selected.id && api(config.family).get() === selectedFamily) {
                            picker.dispatchEvent(new CustomEvent('font-selected', { detail: { source: 'wordpress', slug } }));
                            importMessage = 'Hosted font is ready. Wait for compilation, then Publish to apply it.';
                        } else {
                            importMessage = 'Font imported into WordPress. Your newer font choice has been kept.';
                        }
                    } catch (error) {
                        importMessage = (error.message || 'Import failed.') + ' Your current font choice has been kept. Retry or use Add or manage fonts in the picker.';
                    } finally {
                        importing = false;
                        Object.values(updates).forEach(update => update());
                    }
                }
                suggestion.find('button').on('click', hostLocally);
                picker.addEventListener('font-host-locally', hostLocally);
                picker.addEventListener('font-selected', function(event) {
                    if (event.detail.source !== 'wordpress') return;
                    const slug = event.detail.slug;
                    if (!fonts[slug]) return;
                    window.picostrapApplyingWordPressFont = true;
                    try {
                        api('picostrap_wp_font_' + role).set(slug);
                        api(config.family).set(fonts[slug].sass);
                    } finally {
                        window.picostrapApplyingWordPressFont = false;
                    }
                    window.picostrapRefreshFontLoading(role);
                    api.previewer.refresh();
                });
                picker.addEventListener('hosted-fonts-refresh', async function(event) {
                    try {
                        await refreshFonts();
                        api.previewer.refresh();
                        event.detail.complete();
                    } catch (error) {
                        event.detail.complete(data.error);
                    }
                });
                update();
            });
        });
    });
})(jQuery, wp.customize);
