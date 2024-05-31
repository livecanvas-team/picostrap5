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
                        <div class="tag category-tag"> ${font.category}</div>
                        ${font.variable ? `<div class="tag variable-tag"><span >Variable</span></div>` : ''}
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
                    margin-top:8px;
                    text-align:right;
                }
                .tag {
                    background-color: #ebedf1;
                    border-radius: 4px;
                    padding: 2px 8px;
                    margin-right: 5px;
                    font-size: 0.9em;
                    display: inline-block;
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
