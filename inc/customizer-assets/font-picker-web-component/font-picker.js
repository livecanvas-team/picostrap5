class FontPicker extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.fonts = [];
        this.selectedFont = null;
        this.hostedFonts = {};
        this.hostedConfig = null;
        this.previewStyles = new Map();
        this.openModal = this.openModal.bind(this);
    }

    connectedCallback() {
        this.render();
        const apiUrl = this.getAttribute('data-fontlist-url');
        if (apiUrl) {
            this.getFontSourceFonts(apiUrl);
        } else {
            console.error('data-fontlist-url attribute is missing.');
        }
        this.setupButtonListener();

    }

    disconnectedCallback() {
        this.removeButtonListener();
        this.fontObserver?.disconnect();
    }

    setHostedFonts(fonts, config) {
        this.hostedFonts = fonts;
        this.hostedConfig = config;
        const category = this.shadowRoot.getElementById('filterCategory');
        if (!category.querySelector('[value="hosted"]')) {
            category.add(new Option('Hosted fonts', 'hosted'), 1);
        }
        this.refreshList();
    }

    setHostingOffer(font, busy, message) {
        const offer = this.shadowRoot.getElementById('hostingOffer');
        offer.hidden = !font;
        offer.querySelector('p').textContent = message || (font ? font.family + ' is currently loaded from a CDN. Import its Latin, normal-style weights into WordPress and switch this choice to local hosting. Font files are saved now; Publish applies the font choice.' : '');
        offer.querySelector('button').disabled = busy;
    }

    refreshList() {
        const hosted = this.shadowRoot.getElementById('filterCategory').value === 'hosted';
        this.shadowRoot.getElementById('hostedHelp').hidden = !hosted;
        this.shadowRoot.getElementById('filterVariable').disabled = hosted;
        this.shadowRoot.getElementById('catalogStatus').textContent = '';
        if (hosted) this.displayHostedFonts();
        else {
            this.displayFonts(this.fonts);
            this.filterTable();
            if (!this.fonts.length) this.shadowRoot.getElementById('catalogStatus').textContent = this.remoteError ? 'Remote fonts could not be loaded. Hosted fonts remain available.' : 'Loading remote fonts…';
        }
    }

    displayHostedFonts() {
        this.fontObserver?.disconnect();
        const list = this.shadowRoot.getElementById('fontList');
        list.replaceChildren();
        Object.entries(this.hostedFonts).forEach(([slug, font]) => {
            const row = document.createElement('button');
            row.type = 'button';
            row.className = 'font-row';
            row.dataset.fontFamily = font.name;
            row.dataset.fontCategory = 'hosted';
            row.setAttribute('aria-pressed', String(slug === this.hostedConfig.selected));
            const name = document.createElement('span');
            name.className = 'font-name';
            name.textContent = font.name;
            const preview = document.createElement('span');
            preview.className = 'font-preview';
            preview.textContent = "The quick brown fox jumps over the lazy dog's back.";
            preview.style.fontFamily = JSON.stringify(font.family) + ', sans-serif';
            const details = document.createElement('span');
            details.className = 'font-details';
            details.textContent = 'Enabled variants: ' + font.variants;
            row.append(name, preview, details);
            row.addEventListener('click', () => {
                this.selectedFont = { source: 'wordpress', slug, family: font.family, sass: font.sass };
                this.dispatchEvent(new CustomEvent('font-selected', { detail: this.selectedFont }));
                this.closeModal();
            });
            list.append(row);
        });
        if (!list.children.length) this.shadowRoot.getElementById('catalogStatus').textContent = this.hostedConfig.empty;
        this.filterTable();
    }

    refreshHostedFonts() {
        const button = this.shadowRoot.getElementById('refreshHosted');
        button.disabled = true;
        this.shadowRoot.getElementById('catalogStatus').textContent = 'Refreshing hosted fonts…';
        this.dispatchEvent(new CustomEvent('hosted-fonts-refresh', { detail: { complete: error => {
            button.disabled = false;
            if (error) this.shadowRoot.getElementById('catalogStatus').textContent = error;
            else this.refreshList();
        } } }));
    }

    openFontLibrary(event) {
        event.preventDefault();
        const dialog = this.shadowRoot.getElementById('libraryModal');
        const frame = this.shadowRoot.getElementById('libraryFrame');
        if (!frame.src) frame.src = this.hostedConfig.manageUrl;
        this.shadowRoot.getElementById('libraryFallback').href = this.hostedConfig.libraryUrl || this.hostedConfig.manageUrl;
        dialog.showModal();
    }

    async getFontSourceFonts(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.fonts = await response.json();
            this.refreshList();
        } catch (error) {
            console.error('Failed to fetch fonts:', error);
            this.remoteError = true;
            this.refreshList();
        }
    }

    displayFonts(fonts) {
        const fontList = this.shadowRoot.getElementById('fontList');
        this.fontObserver?.disconnect();
        fontList.innerHTML = ''; // Clear existing rows
        fonts.forEach(font => {
            const fontElement = document.createElement('div');
            fontElement.classList.add('font-row');
            fontElement.tabIndex = 0;
            fontElement.setAttribute('role', 'button');
            fontElement.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.selectFont(font.id); }
            });
            fontElement.setAttribute('data-font-id', font.id);
            fontElement.setAttribute('data-font-family', font.family);
            fontElement.setAttribute('data-font-subsets', font.subsets.join(', '));
            fontElement.setAttribute('data-font-weights', font.weights.join(', '));
            fontElement.setAttribute('data-font-styles', font.styles.join(', '));
            fontElement.setAttribute('data-font-defSubset', font.defSubset);
            fontElement.setAttribute('data-font-variable', font.variable);
            fontElement.setAttribute('data-font-category', font.category);
            fontElement.setAttribute('data-font-type', font.type);
            fontElement.innerHTML = `
                <div>
                    <div class="font-name" style="font-family: '${font.family}', sans-serif;">${font.family}</div>
                    <div class="font-preview" style="font-family: '${font.family}', sans-serif;">The quick brown fox jumps over the lazy dog's back.</div>
                </div>
                 <div class="font-details">
                    <div class="weights"><b>Weights:</b> ${font.weights.join(', ')}</div>
                    <div class="styles"><b>Styles:</b> ${font.styles.join(', ')} </div>  
                    <!-- <div class="subsets"><b>Subsets:</b> ${font.subsets.join(', ')}</div>  -->
                    <!-- <div class="subset"><b>Default Subset:</b> ${font.defSubset} </div>  -->
                    <div class="tags">
                        <div class="tag category-tag ${font.category} "> ${font.category}</div>
                        ${font.variable ? `<div class="tag variable-tag">
                       
                        <svg xmlns="http://www.w3.org/2000/svg" width="54" height="12" fill="currentColor" class="ng-star-inserted"><path _ngcontent-ng-c3107572908="" d="M1 1h2.7l1.7 6.5a11.8 11.8 0 0 1 .4 2l.1-.6.1-.5L8.1 1h.7l-3 10h-2L1 1ZM9.5 9c0-.8.4-1.4 1.3-1.8.8-.4 2-.6 3.5-.6h3.2v-.4c0-.5-.3-.8-.7-1-.5-.2-1.2-.3-2.2-.3a7 7 0 0 0-2.2.2c-.5.2-.7.4-.7.8H10c0-.7.4-1.3 1.2-1.6.8-.4 2-.6 3.4-.6s2.4.2 3.2.6c.8.5 1.1 1 1.1 1.9v3.3c0 .3 0 .5.2.8 0 .3.1.5.3.7v.1h-1.6a1.8 1.8 0 0 1-.4-1 3 3 0 0 1-1.5.8c-.7.2-1.6.3-2.7.3-1.2 0-2.1-.2-2.8-.5-.7-.4-1-1-1-1.7Zm1.6-.1c0 .4.2.7.6.9.5.2 1.2.3 2.2.3 1 0 1.8-.2 2.5-.5s1-.7 1-1v-1h-2.8c-1.1 0-2 .1-2.6.4-.6.2-1 .5-1 .9Zm9.7 2.2V3.8h2.4v.8c.2-.3.5-.5 1-.7a3 3 0 0 1 2-.2v1.8a3.7 3.7 0 0 0-.8-.1 2.4 2.4 0 0 0-2 1.2V11h-2.6Zm6.4 0V3.8h2V11h-2Zm-.1-9.2c0-.3 0-.6.2-.8.2-.2.5-.3.9-.3.3 0 .6.1.8.3.2.2.3.5.3.8 0 .3 0 .5-.3.7-.2.2-.5.3-.8.3-.4 0-.7 0-.9-.3a1 1 0 0 1-.2-.7Zm3.5 6.9c0-.8.2-1.5.7-1.9a3 3 0 0 1 2-.6h1.2v-1c0-.3 0-.6-.2-.8-.1-.2-.4-.3-.7-.3-.3 0-.5.1-.7.3l-.2.9H31c0-.7.3-1.3.8-1.8s1.1-.7 2-.7a3 3 0 0 1 2 .6c.4.4.6 1 .6 2v4l.1.8.2.7v.1h-1.8l-.1-.4-.1-.6c-.1.3-.4.6-.7.8a2 2 0 0 1-1.2.3c-.6 0-1.1-.2-1.5-.6-.4-.3-.6-1-.6-1.8Zm1.8-.1c0 .4 0 .7.2 1 .2.2.4.3.7.3.3 0 .6-.2.8-.5.3-.2.4-.6.4-1V7.2h-1c-.3 0-.6.1-.8.4-.2.2-.3.5-.3 1Zm5.7 2.4V.5h1.5v4c.1-.2.4-.5.7-.6.2-.2.6-.2 1-.2.7 0 1.4.3 1.8 1 .5.7.7 1.5.7 2.6v.2c0 1-.2 2-.7 2.6-.4.7-1 1-1.8 1-.4 0-.8 0-1-.2-.4-.2-.6-.4-.8-.7v.8H38Zm1.5-2c0 .2.2.5.5.7.2.2.5.3.8.3.5 0 .9-.2 1.1-.6.2-.4.4-1 .4-1.7v-.6c0-.7-.2-1.3-.4-1.7-.2-.4-.6-.6-1-.6s-.7.1-1 .3c-.2.3-.4.5-.4.8v3Zm5.8 2V.5h1V11h-1Zm2.7-3.5v-.2c0-1.2.2-2 .7-2.7.5-.7 1.2-1 2-1s1.4.3 1.9.9c.4.5.6 1.4.6 2.7v.3h-5v-.6h4.4c0-.9-.2-1.6-.5-2-.2-.4-.7-.7-1.5-.7-.6 0-1.1.3-1.5.8-.3.5-.5 1.2-.5 2v.7c0 1 .2 1.6.6 2.1.4.5 1 .8 1.7.8.4 0 .8-.1 1-.3.4-.1.6-.4.9-.7l.5.4c-.3.4-.6.7-1 .9a3 3 0 0 1-1.4.3c-1 0-1.6-.3-2.2-1-.5-.6-.7-1.5-.7-2.7Z"></path></svg>
                      
                        </div>` : ''}
                        <!-- <div class="tag type-tag"> ${font.type}</div> -->
                    </div>
                </div>
                <div class="css-import" style="display: none;"></div>
            `;
            fontElement.addEventListener('click', () => this.selectFont(font.id));
            fontList.appendChild(fontElement);
        });

        this.observeFonts();
    }

    async fetchFontDetails(fontId) {
        const url = `https://api.fontsource.org/v1/fonts/${fontId}`;
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch font details:', error);
        }
    }

    generateCssSnippet(font) {
        const unicodeRange = font.unicodeRange ? font.unicodeRange.latin : '';

        if (font.variable) {
            const weightRange = font.weights.join(' ');

            return ` @font-face {
    font-family: '${font.family}';
    font-style: normal;
    font-display: swap;
    font-weight: ${weightRange};
    src: url(https://cdn.jsdelivr.net/fontsource/fonts/${font.id}:vf@latest/latin-wght-normal.woff2) format('woff2-variations');
    unicode-range: ${unicodeRange};
    }
            `;
        } else {
            return font.weights.map(weight => `
                @font-face {
                    font-family: '${font.family}';
                    font-style: normal;
                    font-display: swap;
                    font-weight: ${weight};
                    src: url(https://cdn.jsdelivr.net/fontsource/fonts/${font.id}@latest/latin-${weight}-normal.woff2) format('woff2');
                    unicode-range: ${unicodeRange};
                }
            `).join('\n');
        }
    }

    async loadCssSnippet(target) {
        const fontId = target.getAttribute('data-font-id');
        const fontDetails = await this.fetchFontDetails(fontId);
        if (!fontDetails) return;
        const cssSnippet = this.generateCssSnippet(fontDetails);
        const style = document.createElement('style');
        // Preview remote fonts under an alias so they cannot override a hosted family of the same name.
        const previewFamily = 'Picostrap Preview ' + fontId;
        style.textContent = cssSnippet.replaceAll("font-family: '" + fontDetails.family + "';", "font-family: '" + previewFamily + "';");
        if (!this.previewStyles.has(fontId)) {
            document.head.appendChild(style);
            this.previewStyles.set(fontId, style);
        }
        target.querySelector('.font-name').style.fontFamily = `'${previewFamily}', sans-serif`;
        target.querySelector('.font-preview').style.fontFamily = `'${previewFamily}', sans-serif`;
        target.querySelector('.css-import').textContent = cssSnippet;
    }

    observeFonts() {
        const options = {
            root: this.shadowRoot.getElementById('modalContent'),
            rootMargin: '0px',
            threshold: 0.1
        };

        this.fontObserver?.disconnect();
        const observer = this.fontObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadCssSnippet(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, options);

        this.shadowRoot.querySelectorAll('.font-row').forEach(row => {
            observer.observe(row);
        });
    }

    filterTable() {
        const filterName = this.shadowRoot.getElementById('filterName').value.toLowerCase();
        const filterCategory = this.shadowRoot.getElementById('filterCategory').value;
        const filterVariable = this.shadowRoot.getElementById('filterVariable').value;
        const rows = this.shadowRoot.querySelectorAll('.font-row');
        let visible = 0;

        rows.forEach(row => {
            const family = row.getAttribute('data-font-family').toLowerCase();
            const category = row.getAttribute('data-font-category');
            const variable = row.getAttribute('data-font-variable');
            const matchesName = family.includes(filterName);
            const matchesCategory = !filterCategory || category === filterCategory;
            const matchesVariable = filterCategory === 'hosted' || !filterVariable || variable === filterVariable;

            if (matchesName && matchesCategory && matchesVariable) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });
        const status = this.shadowRoot.getElementById('catalogStatus');
        if (rows.length && !visible) status.textContent = 'No fonts match your search.';
        else if (status.textContent === 'No fonts match your search.') status.textContent = '';
    }

    async selectFont(fontId) {
        const selectedFont = this.fonts.find(font => font.id === fontId);
        const fontDetails = await this.fetchFontDetails(fontId);
        if (!fontDetails) return;
        const cssSnippet = this.generateCssSnippet(fontDetails);
        selectedFont.cssImport = cssSnippet;
        this.selectedFont = selectedFont;
        this.dispatchEvent(new CustomEvent('font-selected', { detail: this.selectedFont }));
        // console.log('Font selected:', this.selectedFont);
        this.closeModal();
    }

    openModal(event) {
        if (event) {
            event.preventDefault();
        }
        if (this.hostedConfig?.selected) this.shadowRoot.getElementById('filterCategory').value = 'hosted';
        this.refreshList();
        this.shadowRoot.getElementById('modal').showModal();
        this.shadowRoot.getElementById('filterName').focus();
    }

    closeModal() {
        this.shadowRoot.getElementById('modal').close();
    }

    setupButtonListener() {
        const buttonSlot = this.shadowRoot.querySelector('slot[name="button"]');
        buttonSlot.addEventListener('slotchange', () => {
            const button = buttonSlot.assignedElements()[0];
            if (button) {
                button.addEventListener('click', this.openModal);
            }
        });
    }

    removeButtonListener() {
        const buttonSlot = this.shadowRoot.querySelector('slot[name="button"]');
        const button = buttonSlot.assignedElements()[0];
        if (button) {
            button.removeEventListener('click', this.openModal);
        }
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    font-family: Arial, sans-serif;
                    font-weight: 400;
                }
                ::slotted(button) {
                    float:right;  /* ONLY FOR THE CUSTOMIZE */
                    padding: 0px 10px;
                    font-size: 11px !important;
                    min-height: 30px;
                    font-weight: 400 !important;
                    color: #2271b1;
                    border-color: #2271b1;
                    background: #f6f7f7;
                    vertical-align: top;
                    cursor: pointer;
                    border-width: 1px;
                    border-style: solid;
                    border-radius: 3px;
                    -webkit-appearance: none;
                    white-space: nowrap;
                    box-sizing: border-box;


                }
                ::slotted(button:hover) {
                    background: #f0f0f1;
                    border-color: #0a4b78;
                    color: #0a4b78;
                }
                dialog {
                    color: #1d2327;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                    padding: 0;
                    border: 0;
                    border-radius: 12px;
                    box-sizing: border-box;
                    overflow: hidden;
                    background: #fff;
                    width: min(1200px, 94vw);
                    max-width: none;
                    max-height: 90dvh;
                    box-shadow: 0 12px 40px rgba(0,0,0,.2);
                }
                dialog::backdrop { background: rgba(0,0,0,.45); }
                #modalContent { display: flex; flex-direction: column; height: 84dvh; min-height: 0; }
                #libraryModal { height: 90dvh; max-height: calc(100dvh - 32px); }
                #libraryModal[open] { display: flex; flex-direction: column; }
                #libraryModal > .modal-header, #libraryModal > .hosted-help { flex: 0 0 auto; }
                #libraryFrame { display: block; flex: 1 1 0%; width: 100%; height: 0; min-height: 0; border: 0; border-radius: 0; background: #fff; }
                .hosted-help { padding: 16px 20px; border-bottom: 1px solid #dcdcde; font-size: 14px; line-height: 1.6; }
                .hosted-help[hidden] { display: none; }
                .hosted-help p { margin: 0 0 10px; }
                .hosted-actions { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
                .hosted-actions a, .library-fallback { color: #2271b1; text-underline-offset: 3px; }
                .hosted-actions button { padding: 6px 10px; border: 1px solid #8c8f94; border-radius: 3px; background: #f6f7f7; color: #1d2327; cursor: pointer; }
                #hostedHelp { padding: 20px; background: #f6f7f7; }
                .hosted-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
                .hosted-intro { min-width: 0; }
                .hosted-intro h3 { margin: 0 0 4px; font-size: 16px; font-weight: 600; line-height: 1.4; }
                .hosted-intro p { margin: 0; max-width: 65ch; color: #50575e; font-size: 13px; }
                .hosted-toolbar .hosted-actions { flex: 0 0 auto; gap: 8px; }
                #hostedHelp .hosted-actions button { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 40px; padding: 8px 12px; font: inherit; font-size: 13px; font-weight: 500; line-height: 1.4; }
                #hostedHelp .hosted-actions svg { width: 16px; height: 16px; flex: none; }
                #hostedHelp #manageHosted { background: #2271b1; border-color: #2271b1; color: #fff; }
                #hostedHelp #manageHosted:hover { background: #135e96; border-color: #135e96; }
                #hostedHelp #refreshHosted { background: transparent; border-color: transparent; color: #2271b1; }
                #hostedHelp #refreshHosted:hover { background: #e8edf2; color: #135e96; }
                #hostedHelp #refreshHosted:disabled { color: #646970; background: transparent; cursor: wait; }
                .hosted-instructions { margin-top: 16px; padding-top: 12px; border-top: 1px solid #dcdcde; }
                .hosted-instructions summary { display: flex; align-items: center; gap: 6px; width: fit-content; color: #50575e; font-size: 13px; font-weight: 500; cursor: pointer; list-style: none; }
                .hosted-instructions summary::-webkit-details-marker { display: none; }
                .hosted-instructions summary svg { width: 14px; height: 14px; }
                .hosted-instructions[open] summary svg { transform: rotate(90deg); }
                .hosted-instructions summary:hover { color: #2271b1; }
                .hosted-instructions h4 { margin: 16px 0 4px; font-size: 13px; font-weight: 600; }
                .hosted-instructions p { max-width: 75ch; color: #50575e; font-size: 13px; }
                #catalogStatus { font-size: 14px; line-height: 1.6; }
                #catalogStatus:empty { display: none; }
                .font-row { text-align: left; background: white; color: inherit; font: inherit; }
                .font-row[aria-pressed="true"] { border-color: #2271b1; }
                :focus-visible { outline: 2px solid #2271b1; outline-offset: 2px; }
                @media(max-width: 700px) {
                    #filterContainer { flex-wrap: wrap; gap: 8px; }
                    #filterContainer input, #filterContainer select { flex: 1 1 45%; min-width: 0; width: 100%; }
                    #filterContainer label { font-size: 12px; }
                    .modal-body { padding: 12px; }
                    .hosted-help { padding: 12px; }
                    #hostedHelp { padding: 16px; }
                    .hosted-toolbar { align-items: flex-start; flex-direction: column; gap: 12px; }
                    .hosted-toolbar .hosted-actions { flex-wrap: wrap; }
                }
                .modal-header {
                    padding: 12px 20px;
                    background-color: #f9fafb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 1px solid #ced6de;
                }
                .modal-body {
                    flex: 1;
                    min-height: 0;
                    overflow-y: auto;
                    padding: 20px;
                }
                .close {
                    color: #50575e;
                    background: transparent;
                    border: 0;
                    min-width: 44px;
                    min-height: 44px;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .close:hover,
                .close:focus {
                    color: #1d2327;
                    text-decoration: none;
                }
                
                #fontList {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                /* Grid for medium screen >= 768px */
                @media (min-width: 768px) {
                    #fontList {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }

                /* Grid for big screen >= 1200px */
                @media (min-width: 1200px) {
                    #fontList {
                        grid-template-columns: repeat(3, 1fr);
                        gap:18px
                    }
                }

                /* Grid for big screen >= 1400px */
                @media (min-width: 1400px) {
                    #fontList {
                        grid-template-columns: repeat(4, 1fr);
                        gap:18px
                    }
                }


                .font-row {
                    padding: 12px;
                    border: 1px solid #eaedf1;
                    cursor: pointer;
                    display: flex;
                    flex-direction: column;
                    border-radius:4px;
                    min-height: 12rem;
                    justify-content: space-between;
                    gap: 12px;
                }
                .font-row:hover {
                    background-color: #f9fafb;
                }
                .font-name {
                    font-size: 2em;
                    font-weight: bold;
                }
                .font-preview {
                    font-size: 1.5em;
                    font-weight:400;
                    line-height: 1.5;
                }
                .font-details {
                    font-size: 0.8em;
                    color: #666;
                    font-weight:400;
                }
                .tags {
                    margin-top: 8px;
                    display: flex;
                    justify-content: end;
                    gap: 3px;
                    align-items: stretch;

                    .serif {font-family:serif;}
                    .sans-serif {font-family:sans-serif;}
                    .display {font-family:Impact;}
                    .handwriting {font-family:Comic Sans MS;font-style:italic;letter-spacing:1px}
                }
                .tag {
                    border-radius: 4px;
                    padding: 2px 8px;
                    font-size: 0.9em;
                    display: inline-block;
                }

                .variable-tag svg {
                    vertical-align: middle;
                    height: 14px;   
                }

                .category-tag {
                    background: transparent;
                    border: 1px solid #eee;
                }

                #filterContainer {
                    background-color: #f9fafb;
                    z-index: 1;
                    padding: 15px;
                    border-bottom: 1px solid #ced6de;
                    display: flex;
                    gap: 15px;
                    position: sticky;
                    top: 0;
                    align-items: center; /* Align vertically */
                }
                #filterContainer input, #filterContainer select {
                    padding: 10px;
                    border: 1px solid #ced6de;
                    border-radius: 4px;
                    flex: 1;
                }
                #filterContainer label {
                    font-weight: bold;
                }
            </style>
            <slot name="button">
                <button id="fontChoiceButton">Choose Font...</button>
            </slot>
            <dialog id="modal" aria-labelledby="pickerTitle">
                <div id="modalContent">
                    <div class="modal-header">
                        <h2 id="pickerTitle" style="margin:0;font-size:18px">Choose Font</h2>
                        <button type="button" class="close" aria-label="Close font picker">&times;</button>
                    </div>
                    <div id="filterContainer">
                        <label for="filterName">Name:</label>
                        <input type="text" id="filterName" oninput="this.getRootNode().host.filterTable()">
                        <label for="filterCategory">Category:</label>
                        <select id="filterCategory" onchange="this.getRootNode().host.refreshList()">
                            <option value="">All remote fonts</option>
                            <option value="serif">Serif</option>
                            <option value="sans-serif">Sans Serif</option>
                            <option value="display">Display</option>
                            <option value="handwriting">Handwriting</option>
                            <option value="monospace">Monospace</option>
                        </select>
                        <label for="filterVariable">Kind:</label>
                        <select id="filterVariable" onchange="this.getRootNode().host.filterTable()">
                            <option value="true">Variable only</option>
                            <option value="">All</option>
                            <option value="false">Non-variable only</option>
                        </select>
                    </div>
                    <aside class="hosted-help" id="hostedHelp" aria-labelledby="hostedHelpTitle" hidden>
                        <div class="hosted-toolbar">
                            <div class="hosted-intro"><h3 id="hostedHelpTitle">Your hosted fonts</h3><p>Choose an enabled family below, or add a font to your WordPress library.</p></div>
                            <div class="hosted-actions">
                                <button type="button" id="manageHosted" aria-haspopup="dialog"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M10 4v12M4 10h12"/></svg>Add or manage fonts</button>
                                <button type="button" id="refreshHosted" title="Use after changes made in another tab. The list refreshes automatically when you close the library."><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M16 7a6.2 6.2 0 1 0 .2 5M16 3v4h-4"/></svg>Refresh list</button>
                            </div>
                        </div>
                        <details class="hosted-instructions">
                            <summary><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 3 5 5-5 5"/></svg>How to add and use fonts</summary>
                            <h4>Install or upload in WordPress</h4><p>Open Add or manage fonts, install or upload a family, enable its weights and styles, then click Update. Close the library to refresh this list automatically. Select a family below and Publish after the preview finishes compiling.</p>
                            <h4>Bring a CDN font onto your site</h4><p>Choose a remote font in this picker, then click Host locally beneath its family field or inside the picker. The import copies Latin, normal-style variants into WordPress. Wait for the import and compilation, then Publish.</p>
                            <p>Your font-weight settings stay unchanged. Enable matching variants in the library. Font Library changes are saved immediately; Picostrap font choices require Publish.</p>
                        </details>
                    </aside>
                    <aside class="hosted-help" id="hostingOffer" hidden><p></p><div class="hosted-actions"><button type="button" id="hostFontLocally">Host locally</button></div></aside>
                    <div class="modal-body">
                        <p id="catalogStatus" role="status"></p>
                        <div id="fontList"></div>
                    </div>
                </div>
            </dialog>
            <dialog id="libraryModal" aria-labelledby="libraryTitle">
                <div class="modal-header"><h2 id="libraryTitle" style="margin:0;font-size:18px">WordPress Font Library</h2><button type="button" class="close" id="closeLibrary" aria-label="Return to font picker">&times;</button></div>
                <div class="hosted-help"><p>Install or upload a font, enable its variants and click Update. Library changes are saved immediately; your Picostrap font choices still require Publish.</p><a class="library-fallback" id="libraryFallback" target="_blank" rel="noopener noreferrer">Open Font Library in a new tab</a></div>
                <iframe id="libraryFrame" title="Manage WordPress fonts"></iframe>
            </dialog>
        `;
        this.shadowRoot.querySelector('#modal .close').addEventListener('click', () => this.closeModal());
        this.shadowRoot.getElementById('manageHosted').addEventListener('click', event => this.openFontLibrary(event));
        this.shadowRoot.getElementById('hostFontLocally').addEventListener('click', () => this.dispatchEvent(new CustomEvent('font-host-locally')));
        this.shadowRoot.getElementById('refreshHosted').addEventListener('click', () => this.refreshHostedFonts());
        const library = this.shadowRoot.getElementById('libraryModal');
        this.shadowRoot.getElementById('closeLibrary').addEventListener('click', () => library.close());
        library.addEventListener('close', () => {
            this.shadowRoot.getElementById('filterCategory').value = 'hosted';
            this.refreshHostedFonts();
        });
    }
}

customElements.define('font-picker', FontPicker);
