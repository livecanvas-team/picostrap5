/* global picostrapWordPressFontData */
(function() {
    'use strict';
    const rest = options => wp.apiFetch(options);
    const signature = face => [face.fontStyle || 'normal', String(face.fontWeight || '400'), face.unicodeRange || ''].join('|');

    // Upload through WordPress's Font Library API; core validates and stores each font file.
    window.picostrapHostFontLocally = async function(font, progress) {
        const data = picostrapWordPressFontData;
        if (!data.canImport || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(font.id)) throw new Error('This font cannot be imported automatically. Use the WordPress Font Library to upload it.');
        const contextResponse = await fetch(data.ajaxUrl, {
            method: 'POST', credentials: 'same-origin',
            body: new URLSearchParams({ action: 'picostrap_font_import_context', nonce: data.nonce })
        });
        const contextResult = await contextResponse.json();
        if (!contextResponse.ok || !contextResult.success) throw new Error(contextResult.data?.message || 'WordPress could not prepare the font import. Check your permissions and try again.');
        const context = contextResult.data;
        if (context.stylesheet !== data.stylesheet) throw new Error('Activate this theme before importing fonts for it. Your current font choice has been kept.');
        progress('Preparing font files…');
        const metadataResponse = await fetch('https://api.fontsource.org/v1/fonts/' + encodeURIComponent(font.id));
        if (!metadataResponse.ok) throw new Error('Font details could not be loaded. Try again or use the Font Library.');
        const metadata = await metadataResponse.json();
        if (metadata.id !== font.id || metadata.family !== font.family) throw new Error('The font source no longer matches this selection. Choose the font again before importing.');
        const weights = [...new Set((metadata.weights || []).map(Number).filter(weight => Number.isFinite(weight) && weight >= 1 && weight <= 1000))].sort((a,b) => a-b);
        if (!weights.length || weights.length > 20) throw new Error('This font has no supported weight information. Use the Font Library instead.');
        const variants = metadata.variable ? [weights[0] === weights.at(-1) ? String(weights[0]) : weights[0] + ' ' + weights.at(-1)] : weights.map(String);
        const faces = variants.map(weight => ({
            fontFamily: metadata.family, fontStyle: 'normal', fontWeight: weight, fontDisplay: 'swap',
            ...(metadata.unicodeRange?.latin ? { unicodeRange: metadata.unicodeRange.latin } : {})
        }));
        const matches = await rest({ path: '/wp/v2/font-families?slug=' + encodeURIComponent(font.id) + '&per_page=1' });
        let family = matches[0];
        if (!family) {
            const candidates = await rest({ path: '/wp/v2/font-families?search=' + encodeURIComponent(metadata.family) + '&per_page=100' });
            family = candidates.find(item => (item.font_family_settings?.fontFamily || '').trim().replace(/^['"]|['"]$/g, '') === metadata.family);
        }
        if (!family) {
            const body = new FormData();
            body.append('font_family_settings', JSON.stringify({ name: metadata.family, slug: font.id, fontFamily: JSON.stringify(metadata.family) }));
            family = await rest({ path: '/wp/v2/font-families', method: 'POST', body });
        }
        const hostedSlug = family.font_family_settings.slug || font.id;
        const installed = await rest({ path: '/wp/v2/font-families/' + family.id + '/font-faces?per_page=100' });
        const localFaces = [];
        for (let index=0; index<faces.length; index++) {
            const face = faces[index];
            const existing = installed.find(item => {
                const saved = item.font_face_settings;
                return (saved.fontStyle || 'normal') === face.fontStyle && String(saved.fontWeight || '400') === face.fontWeight
                    && (!saved.unicodeRange || saved.unicodeRange === face.unicodeRange);
            });
            if (existing) {
                const sources = [].concat(existing.font_face_settings.src || []);
                if (!sources.length || !sources.every(src => src.startsWith(context.fontDirectoryUrl.replace(/\/$/, '') + '/'))) throw new Error('A matching variant already exists with a different source. Manage it in the Font Library before retrying.');
                localFaces.push(existing.font_face_settings);
                continue;
            }
            progress('Importing font file ' + (index+1) + ' of ' + faces.length + '…');
            const suffix = metadata.variable ? ':vf@latest/latin-wght-normal.woff2' : '@latest/latin-' + face.fontWeight + '-normal.woff2';
            const response = await fetch('https://cdn.jsdelivr.net/fontsource/fonts/' + font.id + suffix);
            if (!response.ok) throw new Error('A font file could not be downloaded. Your CDN font is still selected. Retry to continue the import.');
            const blob = await response.blob();
            if (!blob.size || blob.size > 10*1024*1024) throw new Error('The font file is empty or too large. Use the Font Library instead.');
            const body = new FormData();
            body.append('file', blob, font.id + '-' + index + '.woff2');
            body.append('font_face_settings', JSON.stringify({ ...face, src: 'file' }));
            const saved = await rest({ path: '/wp/v2/font-families/' + family.id + '/font-faces', method: 'POST', body });
            localFaces.push(saved.font_face_settings);
        }
        progress('Enabling hosted fonts…');
        // Read immediately before updating so unrelated global styles and font families survive.
        const stylesPath = '/wp/v2/global-styles/' + context.stylesId;
        const globalStyles = await rest({ path: stylesPath + '?context=edit' });
        const settings = globalStyles.settings || {};
        const typography = settings.typography || {};
        const groups = typography.fontFamilies || {};
        const custom = [...(groups.custom || [])];
        const previousIndex = custom.findIndex(item => item.slug === hostedSlug);
        const previous = previousIndex < 0 ? {} : custom[previousIndex];
        const allFaces = new Map((previous.fontFace || []).map(face => [signature(face), face]));
        localFaces.forEach(face => allFaces.set(signature(face), face));
        const enabled = { ...family.font_family_settings, ...previous, name: metadata.family, slug: hostedSlug, fontFamily: JSON.stringify(metadata.family), fontFace: [...allFaces.values()] };
        if (previousIndex < 0) custom.push(enabled); else custom[previousIndex] = enabled;
        await rest({ path: stylesPath, method: 'POST', data: { settings: { ...settings, typography: { ...typography, fontFamilies: { ...groups, custom } } } } });
        // Make the newly imported family available in the open picker without a page reload.
        if (window.FontFace && document.fonts) localFaces.forEach(face => {
            const source = [].concat(face.src)[0];
            const preview = new window.FontFace(metadata.family, 'url(' + JSON.stringify(source) + ')', { weight: String(face.fontWeight || '400'), style: face.fontStyle || 'normal' });
            preview.load().then(loaded => document.fonts.add(loaded)).catch(() => {});
        });
        return hostedSlug;
    };
})();
