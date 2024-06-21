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
        <option value="Alegreya and Noto Sans SC">Alegreya and Noto Sans SC</option>
        <option value="Arimo and Syne">Arimo and Syne</option>
        <option value="Archivo and Source Sans 3">Archivo and Source Sans 3</option>
        <option value="Cabin and Space Grotesk">Cabin and Space Grotesk</option>
        <option value="Cinzel and Chivo">Cinzel and Chivo</option>
        <option value="Comfortaa and Figtree">Comfortaa and Figtree</option>
        <option value="Dancing Script and Anybody">Dancing Script and Anybody</option>
        <option value="DM Sans and Fira Code">DM Sans and Fira Code</option>
        <option value="Dosis and Lora">Dosis and Lora</option>
        <option value="Exo 2 and Nunito">Exo 2 and Nunito</option>
        <option value="Exo 2 and Overpass">Exo 2 and Overpass</option>
        <option value="Fira Code and JetBrains Mono">Fira Code and JetBrains Mono</option>
        <option value="Heebo and Merriweather">Heebo and Merriweather</option>
        <option value="Inter and Open Sans">Inter and Open Sans</option>
        <option value="Inter and Roboto Mono">Inter and Roboto Mono</option>
        <option value="Inter and Source Sans 3">Inter and Source Sans 3</option>
        <option value="Josefin Sans and Cabin">Josefin Sans and Cabin</option>
        <option value="Jost and Noto Sans JP">Jost and Noto Sans JP</option>
        <option value="Jost and Source Sans 3">Jost and Source Sans 3</option>
        <option value="Karla and Source Sans 3">Karla and Source Sans 3</option>
        <option value="Karla and Space Grotesk">Karla and Space Grotesk</option>
        <option value="League Spartan and EB Garamond">League Spartan and EB Garamond</option>
        <option value="Lexend and Work Sans">Lexend and Work Sans</option>
        <option value="Lora and Roboto Flex">Lora and Roboto Flex</option>
        <option value="Manrope and Fira Code">Manrope and Fira Code</option>
        <option value="Manrope and Inter">Manrope and Inter</option>
        <option value="Manrope and Overpass">Manrope and Overpass</option>
        <option value="Manrope and Public Sans">Manrope and Public Sans</option>
        <option value="Merriweather Sans and Lexend">Merriweather Sans and Lexend</option>
        <option value="Merriweather Sans and Quicksand">Merriweather Sans and Quicksand</option>
        <option value="Montserrat and Nunito">Montserrat and Nunito</option>
        <option value="Montserrat and Roboto Mono">Montserrat and Roboto Mono</option>
        <option value="Montserrat and Source Sans 3">Montserrat and Source Sans 3</option>
        <option value="Mulish and DM Sans">Mulish and DM Sans</option>
        <option value="Mulish and Inconsolata">Mulish and Inconsolata</option>
        <option value="Mulish and Roboto Mono">Mulish and Roboto Mono</option>
        <option value="Nunito and DM Sans">Nunito and DM Sans</option>
        <option value="Nunito and Inter">Nunito and Inter</option>
        <option value="Nunito and Montserrat">Nunito and Montserrat</option>
        <option value="Nunito Sans and Roboto Flex">Nunito Sans and Roboto Flex</option>
        <option value="Nunito Sans and Roboto Slab">Nunito Sans and Roboto Slab</option>
        <option value="Nunito Sans and Work Sans">Nunito Sans and Work Sans</option>
        <option value="Noto Sans and DM Sans">Noto Sans and DM Sans</option>
        <option value="Noto Sans and Roboto Flex">Noto Sans and Roboto Flex</option>
        <option value="Noto Sans Lao and Nunito Sans">Noto Sans Lao and Nunito Sans</option>
        <option value="Noto Sans TC and Noto Serif">Noto Sans TC and Noto Serif</option>
        <option value="Noto Sans Thai and Crimson Pro">Noto Sans Thai and Crimson Pro</option>
        <option value="Oswald and Quicksand">Oswald and Quicksand</option>
        <option value="Outfit and Sora">Outfit and Sora</option>
        <option value="Playfair Display and Comfortaa">Playfair Display and Comfortaa</option>
        <option value="Playfair Display and Source Sans 3">Playfair Display and Source Sans 3</option>
        <option value="Piazzolla and Recursive">Piazzolla and Recursive</option>
        <option value="Poppins and Fira Code">Poppins and Fira Code</option>
        <option value="Poppins and Source Code Pro">Poppins and Source Code Pro</option>
        <option value="Plus Jakarta Sans and Raleway">Plus Jakarta Sans and Raleway</option>
        <option value="Quicksand and Roboto Mono">Quicksand and Roboto Mono</option>
        <option value="Raleway and Public Sans">Raleway and Public Sans</option>
        <option value="Raleway and Roboto Mono">Raleway and Roboto Mono</option>
        <option value="Raleway and Rubik">Raleway and Rubik</option>
        <option value="Roboto Slab and Figtree">Roboto Slab and Figtree</option>
        <option value="Rubik and Inter">Rubik and Inter</option>
        <option value="Rubik and Mulish">Rubik and Mulish</option>
        <option value="Rubik and Noto Sans">Rubik and Noto Sans</option>
        <option value="Rubik and Nunito">Rubik and Nunito</option>
        <option value="Space Grotesk and Inconsolata">Space Grotesk and Inconsolata</option>
        <option value="Space Grotesk and Roboto Mono">Space Grotesk and Roboto Mono</option>
        <option value="Source Code Pro and Noto Sans JP">Source Code Pro and Noto Sans JP</option>
        <option value="Work Sans and Inconsolata">Work Sans and Inconsolata</option>
        <option value="Yanone Kaffeesatz and Grandstander">Yanone Kaffeesatz and Grandstander</option>
  
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

