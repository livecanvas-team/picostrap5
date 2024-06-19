////////DEFINE ICONS //////////

const theStyleGuideIcon = `
	<svg style="margin-right:5px;vertical-align: middle; height:13px; width: 13px; margin-right: 5px; margin-top: -1px; " xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-heading" viewBox="0 0 16 16">
	<path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
	<path d="M3 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-1z"/>
	</svg>`;

const videoTutIcon = `
	<svg style="vertical-align: middle; height:13px; width: 13px; margin-right: 5px; margin-top: -1px; " xmlns="http://www.w3.org/2000/svg" width="3em" height="3em" fill="currentColor" viewBox="0 0 16 16" style="" lc-helper="svg-icon"><path d="M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"></path><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"></path></svg>`;

////////DEFINE FONT COMBINTIONS //////////


const ps_font_combinations_select = `

<div id="cs-font-combi">
  <h2>Font Combinations</h2>
  <span id="_customize-description-picostrap_font_combinations" class="description customize-control-description">Check out <a target="_blank" href="http://fontpair.co/all">FontPair</a> or <a target="_blank" href="https://femmebot.github.io/google-type/">Google Type</a> for more inspiration. </span>
  <select id="_ps_font_combinations" aria-describedby="_customize-description-picostrap_font_combinations" data-customize-setting-link="picostrap_font_combinations">
    <option value="" selected="selected">Choose...</option>
    <optgroup label="Variable Fonts">
        <option value="....">.....</option>
       
    </optgroup>

    <optgroup label="All Fonts">
        <option value="Abel and Ubuntu">Abel and Ubuntu</option>
        <option value="Alegreya and Alegreya">Alegreya and Alegreya</option>
        <option value="Alegreya and Open Sans">Alegreya and Open Sans</option>
        <option value="Barlow Condensed and Montserrat">Barlow Condensed and Montserrat</option>
        <option value="Cabin and Old Standard TT">Cabin and Old Standard TT</option>
        <option value="Cantata One and Imprima">Cantata One and Imprima</option>
        <option value="Clicker Script and EB Garamond">Clicker Script and EB Garamond</option>
        <option value="Cormorant Garamond and Proza Libre">Cormorant Garamond and Proza Libre</option>
        <option value="Crete Round and ABeeZee">Crete Round and ABeeZee</option>
        <option value="Dancing Script and Ledger">Dancing Script and Ledger</option>
        <option value="Didact Gothic and Arimo">Didact Gothic and Arimo</option>
        <option value="DM Serif Display and DM Sans">DM Serif Display and DM Sans</option>
        <option value="Fjalla One and Average">Fjalla One and Average</option>
        <option value="Fjalla One and Cantarell">Fjalla One and Cantarell</option>
        <option value="Francois One and Lato">Francois One and Lato</option>
        <option value="IBM Plex Sans Condensed and IBM Plex Sans">IBM Plex Sans Condensed and IBM Plex Sans</option>
        <option value="Inter and Inter">Inter and Inter</option>
        <option value="Istok Web and Lora">Istok Web and Lora</option>
        <option value="Josefin Sans and Playfair Display">Josefin Sans and Playfair Display</option>
        <option value="Karla and Inconsolata">Karla and Inconsolata</option>
        <option value="Karla and Merriweather">Karla and Merriweather</option>
        <option value="Lato and Merriweather">Lato and Merriweather</option>
        <option value="Libre Baskerville and Libre Baskerville">Libre Baskerville and Libre Baskerville</option>
        <option value="Libre Baskerville and Montserrat">Libre Baskerville and Montserrat</option>
        <option value="Montserrat and Cardo">Montserrat and Cardo</option>
        <option value="Montserrat and Crimson Text">Montserrat and Crimson Text</option>
        <option value="Montserrat and Domine">Montserrat and Domine</option>
        <option value="Montserrat and Hind">Montserrat and Hind</option>
        <option value="Montserrat and Montserrat">Montserrat and Montserrat</option>
        <option value="Montserrat and Neuton">Montserrat and Neuton</option>
        <option value="Montserrat and Playfair Display">Montserrat and Playfair Display</option>
        <option value="Nixie One and Ledger">Nixie One and Ledger</option>
        <option value="Nunito and Lora">Nunito and Lora</option>
        <option value="Nunito and PT Sans">Nunito and PT Sans</option>
        <option value="Nunito Sans and Nunito Sans">Nunito Sans and Nunito Sans</option>
        <option value="Open Sans and Arsenal">Open Sans and Arsenal</option>
        <option value="Oswald and Merriweather">Oswald and Merriweather</option>
        <option value="Oswald and Quattrocento">Oswald and Quattrocento</option>
        <option value="Patua One and Lora">Patua One and Lora</option>
        <option value="Playfair Display and Lato">Playfair Display and Lato</option>
        <option value="Playfair Display and Open Sans">Playfair Display and Open Sans</option>
        <option value="PT Sans and PT Serif">PT Sans and PT Serif</option>
        <option value="Quattrocento and Lora">Quattrocento and Lora</option>
        <option value="Quattrocento and Quattrocento Sans">Quattrocento and Quattrocento Sans</option>
        <option value="Quicksand and EB Garamond">Quicksand and EB Garamond</option>
        <option value="Quicksand and Quicksand">Quicksand and Quicksand</option>
        <option value="Raleway and Merriweather">Raleway and Merriweather</option>
        <option value="Roboto Condensed and Roboto">Roboto Condensed and Roboto</option>
        <option value="Sacramento and Alice">Sacramento and Alice</option>
        <option value="Stint Ultra Expanded and Pontano Sans">Stint Ultra Expanded and Pontano Sans</option>
        <option value="Ultra and PT Serif">Ultra and PT Serif</option>
        <option value="Ubuntu and Lora">Ubuntu and Lora</option>
        <option value="Work Sans and Merriweather">Work Sans and Merriweather</option>
        <option value="Yeseva One and Josefin Sans">Yeseva One and Josefin Sans</option>
    </optgroup>
  </select>
  <br>
  <br>
</div>
`;







//////////// DEFINE LOCAL / SYSTEM FONTS TO SHOW IN FONTPICKER (unused) //////////


const theLocalFonts = ({
	"American Typewriter": {
		"category": "serif",
		"variants": "400,400i,600,600i"
	},
	"Arial": {
		"category": "sans-serif",
		"variants": "400,400i,600,600i"
	},
	/*	"Bradley Hand": {
		   "category": "handwriting",
		   //"variants": "400,400i,600,600i"
		}, */
	"Copperplate": {
		"category": "display",
		"variants": "400,400i,600,600i"
	},
	"Courier New": {
		"category": "monospace",
		"variants": "400,400i,600,600i"
	},
	"Didot": {
		"category": "serif",
		"variants": "400,400i,600,600i"
	},
	"Georgia": {
		"category": "serif",
		"variants": "400,400i,600,600i"
	},
	"Helvetica": {
		"category": "sans-serif",
		"variants": "400,400i,600,600i"
	},
	"Monaco": {
		"category": "sans-serif",
		"variants": "400,400i,600,600i"
	},/*
	"Optima": {
		"category": "serif",
		"variants": "400,400i,600,600i"
	},*/
	"Tahoma": {
		"category": "sans-serif",
		"variants": "400,400i,600,600i"
	},
	"Times New Roman": {
		"category": "serif",
		"variants": "400,400i,600,600i"
	},
	"Trebuchet MS": {
		"category": "sans-serif",
		"variants": "400,400i,600,600i"
	},
	"Verdana": {
		"category": "sans-serif",
		"variants": "400,400i,600,600i",
	}

});

