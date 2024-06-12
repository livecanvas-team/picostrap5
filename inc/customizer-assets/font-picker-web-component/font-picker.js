class FontPicker extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.fonts = [];
        this.selectedFont = null;
        this.handleKeyDown = this.handleKeyDown.bind(this);
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
        document.addEventListener('keydown', this.handleKeyDown);
    }

    disconnectedCallback() {
        this.removeButtonListener();
        document.removeEventListener('keydown', this.handleKeyDown);
    }

    handleKeyDown(event) {
        if (event.key === 'Escape') {
            this.closeModal();
        }
    }

    async getFontSourceFonts(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.fonts = await response.json();
            this.displayFonts(this.fonts);
            this.filterTable(); // Ensure filter is run initially
        } catch (error) {
            console.error('Failed to fetch fonts:', error);
        }
    }

    displayFonts(fonts) {
        const fontList = this.shadowRoot.getElementById('fontList');
        fontList.innerHTML = ''; // Clear existing rows
        fonts.forEach(font => {
            const fontElement = document.createElement('div');
            fontElement.classList.add('font-row');
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
        const cssSnippet = this.generateCssSnippet(fontDetails);
        const style = document.createElement('style');
        style.textContent = cssSnippet;
        document.head.appendChild(style);
        target.querySelector('.font-name').style.fontFamily = `'${fontDetails.family}', sans-serif`;
        target.querySelector('.font-preview').style.fontFamily = `'${fontDetails.family}', sans-serif'`;
        target.querySelector('.css-import').textContent = cssSnippet;
    }

    observeFonts() {
        const options = {
            root: this.shadowRoot.getElementById('modalContent'),
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
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

        rows.forEach(row => {
            const family = row.getAttribute('data-font-family').toLowerCase();
            const category = row.getAttribute('data-font-category');
            const variable = row.getAttribute('data-font-variable');
            const matchesName = family.includes(filterName);
            const matchesCategory = !filterCategory || category === filterCategory;
            const matchesVariable = !filterVariable || variable === filterVariable;

            if (matchesName && matchesCategory && matchesVariable) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    async selectFont(fontId) {
        const selectedFont = this.fonts.find(font => font.id === fontId);
        const fontDetails = await this.fetchFontDetails(fontId);
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
        this.shadowRoot.getElementById('modal').style.display = 'block';
    }

    closeModal() {
        this.shadowRoot.getElementById('modal').style.display = 'none';
    }

    setupButtonListener() {
        const buttonSlot = this.shadowRoot.querySelector('slot[name="button"]');
        buttonSlot.addEventListener('slotchange', () => {
            const button = buttonSlot.assignedElements()[0];
            if (button) {
                button.addEventListener('click', this.openModal.bind(this));
            }
        });
    }

    removeButtonListener() {
        const buttonSlot = this.shadowRoot.querySelector('slot[name="button"]');
        const button = buttonSlot.assignedElements()[0];
        if (button) {
            button.removeEventListener('click', this.openModal.bind(this));
        }
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    font-family: Arial, sans-serif;
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
                #modal {
                    display: none;
                    position: fixed;
                    z-index: 999999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background-color: rgba(0,0,0,0.4);
                }
                #modalContent {
                    background-color: #fefefe;
                    margin: 5% auto;
                    width: 80%;
                    max-height: 80%;
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .modal-header {
                    padding: 12px 20px;
                    background-color: #f9fafb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-top-left-radius: 10px;
                    border-top-right-radius: 10px;
                    border-bottom: 1px solid #ced6de;
                }
                .modal-body {
                    flex: 1;
                    overflow-y: auto;
                    padding: 20px;
                }
                .close {
                    color: #535ac1;;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .close:hover,
                .close:focus {
                    color: #ccc;
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
            <div id="modal">
                <div id="modalContent">
                    <div class="modal-header">
                        <div>Choose Font</div>
                        <span class="close" onclick="this.getRootNode().host.closeModal()">&times;</span>
                    </div>
                    <div id="filterContainer">
                        <label for="filterName">Name:</label>
                        <input type="text" id="filterName" oninput="this.getRootNode().host.filterTable()">
                        <label for="filterCategory">Category:</label>
                        <select id="filterCategory" onchange="this.getRootNode().host.filterTable()">
                            <option value="">All</option>
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
                    <div class="modal-body">
                        <div id="fontList"></div>
                    </div>
                </div>
            </div>
        `;
    }
}

customElements.define('font-picker', FontPicker);
