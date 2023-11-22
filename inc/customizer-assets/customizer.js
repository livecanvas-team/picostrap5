(function($) {

	//DEBOUNCE UTILITY
	function debounce(func, wait, immediate) {
		var timeout;

		return function executedFunction() {
			var context = this;
			var args = arguments;

			var later = function () {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};

			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) func.apply(context, args);
		};
	};

	//FUNCTION TO LOOP ALL COLOR WIDGETS AND SHOW CURRENT COLOR grabbing the exposed css variable from page
	function ps_get_page_colors(){
        
        $("#sub-accordion-section-colors .customize-control-color").each(function(index, el) { //foreach color widget
            if (!$(el).find(".customize-control-description").text().includes("$")) return; //skip element if description does not contain a dollar

			//console.log($(el).find(".customize-control-description").text());

			if ($(el).find(".customize-control-description").text().includes("link-")) return true; //skip element if description does   contain link
			if ($(el).find(".customize-control-description").text().includes("body-")) return true; //skip element if description does   contain body 

            color_name = $(el).find(".customize-control-description .variable-name").text().replace("(", "").replace(")", "").replace("$", "--bs-");
            var color_value = getComputedStyle(document.querySelector("#customize-preview iframe").contentWindow.document.documentElement).getPropertyValue(color_name);
            //console.log(color_name+color_value);

			//append if not already present add a small widget for feedback
			if (!$(el).find(".customizer-current-color").length) $(el).find(".customize-control-title").append("<div class=customizer-current-color>Current</div>");

			//set the color on the widget
			if (color_value) $(el).find(".customizer-current-color").css("border-color", color_value);
        }); //end each
        
    }
	
	function ps_save_css_bundle(){

		//build the request to send via AJAX POST
		const formdata = new FormData();
		const theCss = document.querySelector('#customize-preview iframe').contentWindow.document.querySelector('#picosass-injected-style')?.innerHTML;
		if (   !theCss  || theCss.trim() == '') {
			console.log("No CSS saving necessary, aborting ps_save_css_bundle");
			return false;
		}
		formdata.append("nonce", picostrap_ajax_obj.nonce);
		formdata.append("action", "picostrap_save_css_bundle");
		formdata.append("css", theCss);
		fetch(picostrap_ajax_obj.ajax_url, {
			method: "POST",
			credentials: "same-origin",
			headers: {
				"Cache-Control": "no-cache",
			},
			body: formdata
		}).then(response => response.text())
			.then(response => {
				
				console.log("Saved successfully: " + response);
				 
			}).catch(function (err) {
				console.log("ps_save_css_bundle Error: "+err);
			}); 
			
	} //END FUNCTION  
	
		
	function ps_is_a_google_font(fontFamilyName){
		const google_fonts = JSON.parse(google_fonts_json);
		var fontData = google_fonts.find(function (element) {
			return element.family == fontFamilyName;
		});
		if (!fontData) return false; else return true;
		
	} // end function ps_is_a_google_font


	// FUNCTION TO PREPARE THE HTML CODE SNIPPET THAT LOADS THE (GOOGLE) FONTS
	function ps_update_fonts_import_code_snippet(){
		console.log('Running function ps_update_fonts_import_code_snippet to generate html code for font import:');
		
		//BUILD BASE FONT IMPORT HEAD CODE
		var first_part="";
		if ($("#_customize-input-SCSSvar_font-family-base").val().trim()!='' && ps_is_a_google_font($("#_customize-input-SCSSvar_font-family-base").val().split(',')[0].trim().replace(/"/g, "")) ) {  
			first_part += 'family=' + $("#_customize-input-SCSSvar_font-family-base").val().split(',')[0].trim().replace(/"/g, "").replace(/ /g, "+");
			if ($("#_customize-input-SCSSvar_font-weight-base").val() != '') first_part +=":wght@"+$("#_customize-input-SCSSvar_font-weight-base").val();
		}
		//console.log(first_part); //for debug
		
		//BUILD HEADINGS FONT IMPORT HEAD CODE
		var second_part="";
		if ($("#_customize-input-SCSSvar_headings-font-family").val().trim()!=''  && ps_is_a_google_font($("#_customize-input-SCSSvar_headings-font-family").val().split(',')[0].trim().replace(/"/g, "")) ) {
			second_part += 'family=' + $("#_customize-input-SCSSvar_headings-font-family").val().split(',')[0].trim().replace(/"/g, "").replace(/ /g, "+");
			if ($("#_customize-input-SCSSvar_headings-font-weight").val() != '') second_part +=":wght@"+$("#_customize-input-SCSSvar_headings-font-weight").val();
		}
		//console.log(second_part); //for debug

		var html_code = "";

		if (first_part == "" && second_part == "") {
			//no import code needed
		} else {
			var separator_char = ""; 
			if (first_part != "" && second_part != "") separator_char = "&"; 

			window.fontLoadingUrl = 'https://fonts.googleapis.com/css2?' + first_part + separator_char + second_part +'&display=swap'
			html_code += '<link rel="preconnect" href="https://fonts.googleapis.com">\n';
			html_code += '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n';
			html_code += '<link href="' + window.fontLoadingUrl + '" rel="stylesheet">\n';
			
			//an example: https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,800;1,800&family=Roboto:wght@100;300&display=swap 
		}
			
		//disable alternative font source checkbox
		$("#_customize-input-picostrap_fonts_use_alternative_font_source").prop("checked",false);

		//console.log(html_code);
		
		//populate the textarea with the result
		$("#_customize-input-picostrap_fonts_header_code").val(html_code).change();

	} // end function 
		

	// FUNCTION TO PREPARE THE SCSS CODE assigning all the variables according to  THE WIDGETS VALUES
	function getMainSass() {

		var sass = '';
		
		// loop all input text widgets that have values matched to SCSS vars
		var els = document.querySelectorAll(`[id^='customize-control-SCSSvar'] input[type='text']:not(.cs-fontpicker-input)`);

		for (var i = 0; i < els.length; i++) {

			//for debug purpose, give them a bg color 
			//els[i].style.backgroundColor = "#" + Math.floor(Math.random() * 16777215).toString(16);

			if (els[i].value) {

				let name = els[i].closest("li").getAttribute("id").replace("customize-control-SCSSvar_", "");

				//console.log(name + " " + els[i].value);
				
				sass += `$${name}: ${els[i].value}; `;
				
			} //end if value 

		}

		// loop all checkbox text widgets that have values matched to SCSS vars
		var els = document.querySelectorAll(`[id^='customize-control-SCSSvar'] input[type='checkbox']`);

		for (var i = 0; i < els.length; i++) {

			//for debug purpose, give them a bg color 
			//els[i].style.backgroundColor = "#" + Math.floor(Math.random() * 16777215).toString(16);

			let name = els[i].closest("li").getAttribute("id").replace("customize-control-SCSSvar_", "");
				
			sass += `$${name}: ${(els[i].checked ? 'true' : 'false')}; `;
		}

		//console.log('Variables Sass code: ' + sass);

		return sass + " @import 'main'; ";
	}

	// FUNCTION TO REUPDATE THE SCSS FIELD AND RETRIGGER COMPILER
	function updateScssPreview() {

		var iframeDoc = document.querySelector('#customize-preview iframe').contentWindow.document;

		//build the full SCSS with variables and main import
		var newsass = getMainSass();

		console.log('Update SCSS code to: \n' + newsass);

		iframeDoc.querySelector('#the-scss').innerHTML = newsass; 
		
		function compilingFinished(compiled) { 
			//console.log(compiled);
			// show publishing action buttons
			document.querySelector('#customize-save-button-wrapper').removeAttribute('hidden');
			ps_get_page_colors(); 
		}

		//hide publishing action buttons
		document.querySelector('#customize-save-button-wrapper').setAttribute('hidden', '');

		//trigger picosass compiler
		document.querySelector('#customize-preview iframe').contentWindow.Picosass.Compile({}, compilingFinished); 
	
		//update font loading code as well, if necessary
		if (window.fontLoadingUrl){
			console.log("Update font loading code to add " + window.fontLoadingUrl);
			var style = document.createElement('link');
			style.href = window.fontLoadingUrl;
			style.type = 'text/css';
			style.rel = 'stylesheet';
			iframeDoc.head.append(style);
		}

	}

	//DEBOUNCED VERSION OF THE ABOVE
	var updateScssPreviewDebounced = debounce(function () {
		updateScssPreview();
	}, 1250);

	////////////////////////////////////////// DOCUMENT READY //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$(document).ready(function() {
		
		//TESTBED for G fontsx
		//console.log(ps_is_a_google_font("ABeeZee"));
		//console.log(ps_is_a_google_font("Nunito"));
		
		//hide useless bg color widget
		$("#customize-control-background_color").hide();
		
		//ADD HEADINGS LOOP
		$(".cs-option-group-title").each(function(index, el) { //foreach group title	
			$(el).closest("li.customize-control").prepend(" <h1>"+$(el).text()+"</h1><hr> ");
		}); //end each
		
		//ADD COLORS HEADING 
		$("#customize-control-enable_back_to_top").prepend(" <h1>Opt-in extra features</h1><hr> ");
		
		//add codemirror to header field - does not work
		//wp.codeEditor.initialize(jQuery('#_customize-input-picostrap_header_code'));

		//NOW UNUSED -- ON MOUSEDOWN ON PUBLISH / SAVE BUTTON, (before saving)
		/*
		$("body").on("mousedown", "#customize-save-button-wrapper #save", function() {
			console.log("Clicked Publish"); 
			const compilerFeedback = document.querySelector('#customize-preview iframe').contentWindow.document.querySelector('#picosass-output-feedback').innerHTML;
			if (compilerFeedback.includes('Compiling') || compilerFeedback.includes('error')) {
				alert("Please publish after compilation has completed successfully.");
				return false;
			}
		});			
		*/

		//CHECK IF USING VINTAGE GOOGLE FONTS API V1, REBUILD FONT IMPORT CODE
		if ($("#_customize-input-picostrap_fonts_header_code").val().includes('https://fonts.googleapis.com/css?')){
			console.log("GOOGLE FONTS API V1 is used, let's rebuild the font import header code to update it to v2 syntax");
			ps_update_fonts_import_code_snippet();
		}
		
		//////////////////// LISTEN TO CUSTOMIZER CHANGES ////////////////////////

 		//upon changing of widgets that refer to SCSS variables, trigger a function to updateScssPreview
		//these options use postMessage and all is handled by us in JS
		wp.customize.bind('change', function (setting) {

			if (setting.id.includes("SCSSvar")) {
				updateScssPreviewDebounced();
			}
		});

        // If user navigates inside preview, rebuild and apply SCSS
        wp.customize.previewer.bind('url', function (newUrl) {
            console.log('Preview URL changed to: ' + newUrl);
            updateScssPreviewDebounced();
        });

      
		//////////// USER ACTIONS / UX HELPERS /////////////////
		
		//ON CLICK LINK TO REGENERATE FONT LOADING CODE, DO IT
		$("body").on("click", "#regenerate-font-loading-code", function () {
			ps_update_fonts_import_code_snippet();
		});	

		//ON CHANGE CHECKBOX FOR  USE ALTERNATIVE FONT SOURCE FOR GDPR
		$("body").on("change", "#_customize-input-picostrap_fonts_use_alternative_font_source", function() {
			var html_code = $("#_customize-input-picostrap_fonts_header_code").val();

			if ($(this).prop("checked")) {
				html_code = html_code.replaceAll('fonts.googleapis.com', 'api.fonts.coollabs.io');
				html_code = html_code.replaceAll('<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>', '<!-- -->');
			} else {
				html_code = html_code.replaceAll('api.fonts.coollabs.io', 'fonts.googleapis.com');
				html_code = html_code.replaceAll('<!-- -->', '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>');
			}

			$("#_customize-input-picostrap_fonts_header_code").val(html_code).change();

		});	

		//ON CHANGE FONT LOADING CODE TEXTAREA, IF INSERTED TEXT CONTAINS GOOGLE FONTS DOMAIN, RESET ALTERNATIVE FONT SOURCE CHECKBOX
		$("body").on("input", "#_customize-input-picostrap_fonts_header_code", function () {
			if ($(this).val().includes('fonts.googleapis.com') && $("#_customize-input-picostrap_fonts_use_alternative_font_source").prop("checked")){
				console.log("New textarea content contains 'fonts.googleapis.com' so we will uncheck the GDPR compliance checkbox");
				//disable alternative font source checkbox
				$("#_customize-input-picostrap_fonts_use_alternative_font_source").prop("checked", false);
			}
		});	
		
		//AFTER PUBLISHING CUSTOMIZER CHANGES, SAVE SCSS & CSS
		wp.customize.bind('saved', function( /* data */ ) {
			ps_save_css_bundle();
		});
				
		// USER CLICKS ON COLORS SECTION: run  get page colors routine
		$("body").on("click", "#accordion-section-colors", function() {
			ps_get_page_colors();
		});
		
		//USER CLICKS ENABLE TOPBAR: SET A NICE HTML DEFAULT
		$("body").on("click","#customize-control-enable_topbar",function(){
			if (!$("#_customize-input-enable_topbar").prop("checked")) return;
			var html_default =`
					<a class="text-reset me-2" href = "tel:+1234567890" > <svg style="width:1em;height:1em" viewBox="0 0 24 24">
							<path fill="currentColor" d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"></path>
						</svg> Call us now <span class="d-none d-md-inline" >: 1234567890 </span > </a>

					<a class="text-reset me-2" href="https://wa.me/1234567890"><svg style="width:1em;height:1em" viewBox="0 0 24 24">
							<path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M8.53 7.33C8.37 7.33 8.1 7.39 7.87 7.64C7.65 7.89 7 8.5 7 9.71C7 10.93 7.89 12.1 8 12.27C8.14 12.44 9.76 14.94 12.25 16C12.84 16.27 13.3 16.42 13.66 16.53C14.25 16.72 14.79 16.69 15.22 16.63C15.7 16.56 16.68 16.03 16.89 15.45C17.1 14.87 17.1 14.38 17.04 14.27C16.97 14.17 16.81 14.11 16.56 14C16.31 13.86 15.09 13.26 14.87 13.18C14.64 13.1 14.5 13.06 14.31 13.3C14.15 13.55 13.67 14.11 13.53 14.27C13.38 14.44 13.24 14.46 13 14.34C12.74 14.21 11.94 13.95 11 13.11C10.26 12.45 9.77 11.64 9.62 11.39C9.5 11.15 9.61 11 9.73 10.89C9.84 10.78 10 10.6 10.1 10.45C10.23 10.31 10.27 10.2 10.35 10.04C10.43 9.87 10.39 9.73 10.33 9.61C10.27 9.5 9.77 8.26 9.56 7.77C9.36 7.29 9.16 7.35 9 7.34C8.86 7.34 8.7 7.33 8.53 7.33Z"></path>
						</svg> WhatsApp<span class="d-none d-md-inline">: 1234567890 </span> </a>

					<a class="text-reset me-2" href="mailto:info@yoursite.com"><svg style="width:1em;height:1em" viewBox="0 0 24 24">
							<path fill="currentColor" d="M12,15C12.81,15 13.5,14.7 14.11,14.11C14.7,13.5 15,12.81 15,12C15,11.19 14.7,10.5 14.11,9.89C13.5,9.3 12.81,9 12,9C11.19,9 10.5,9.3 9.89,9.89C9.3,10.5 9,11.19 9,12C9,12.81 9.3,13.5 9.89,14.11C10.5,14.7 11.19,15 12,15M12,2C14.75,2 17.1,3 19.05,4.95C21,6.9 22,9.25 22,12V13.45C22,14.45 21.65,15.3 21,16C20.3,16.67 19.5,17 18.5,17C17.3,17 16.31,16.5 15.56,15.5C14.56,16.5 13.38,17 12,17C10.63,17 9.45,16.5 8.46,15.54C7.5,14.55 7,13.38 7,12C7,10.63 7.5,9.45 8.46,8.46C9.45,7.5 10.63,7 12,7C13.38,7 14.55,7.5 15.54,8.46C16.5,9.45 17,10.63 17,12V13.45C17,13.86 17.16,14.22 17.46,14.53C17.76,14.84 18.11,15 18.5,15C18.92,15 19.27,14.84 19.57,14.53C19.87,14.22 20,13.86 20,13.45V12C20,9.81 19.23,7.93 17.65,6.35C16.07,4.77 14.19,4 12,4C9.81,4 7.93,4.77 6.35,6.35C4.77,7.93 4,9.81 4,12C4,14.19 4.77,16.07 6.35,17.65C7.93,19.23 9.81,20 12,20H17V22H12C9.25,22 6.9,21 4.95,19.05C3,17.1 2,14.75 2,12C2,9.25 3,6.9 4.95,4.95C6.9,3 9.25,2 12,2Z"></path>
						</svg> Email<span class="d-none d-md-inline">: info@yoursite.com</span></a>

					<a class="text-reset me-2" href="https://www.google.com/maps/place/Bangkok,+Thailand/@13.7244416,100.3529157,10z/"><svg style="width:1em;height:1em" viewBox="0 0 24 24">
							<path fill="currentColor" d="M12,2C15.31,2 18,4.66 18,7.95C18,12.41 12,19 12,19C12,19 6,12.41 6,7.95C6,4.66 8.69,2 12,2M12,6A2,2 0 0,0 10,8A2,2 0 0,0 12,10A2,2 0 0,0 14,8A2,2 0 0,0 12,6M20,19C20,21.21 16.42,23 12,23C7.58,23 4,21.21 4,19C4,17.71 5.22,16.56 7.11,15.83L7.75,16.74C6.67,17.19 6,17.81 6,18.5C6,19.88 8.69,21 12,21C15.31,21 18,19.88 18,18.5C18,17.81 17.33,17.19 16.25,16.74L16.89,15.83C18.78,16.56 20,17.71 20,19Z"></path>
						</svg> Map<span class="d-none d-md-inline">: Address</span></a>
						`;
			if ($("#_customize-input-topbar_content").val() == "") $("#_customize-input-topbar_content").val(html_default.trim().replace(/(\r\n|\n|\r)/gm, "")).change();
		}); 

		// FONT COMBINATIONS SELECT ////////////////////////////////////////////

		//ADD UI: the SELECT for FONT BASE
		$("li#customize-control-SCSSvar_font-family-base").prepend(ps_font_combinations_select);

		//USER CLICKS SHOW FONT COMBINATIONS: show the select
		$("body").on("click", "#cs-show-combi", function () {
			//$(".customize-controls-close").click();
            $(this).toggleClass("active");
			$("#cs-font-combi").slideToggle();
		});

		//WHEN A FONT COMBINATION IS CHOSEN
		$("body").on("change", "select#_ps_font_combinations", function() {
			var value = jQuery(this).val(); //Cabin and Old Standard TT
			var arr = value.split(' and ');
			var font_headings = arr[0];
			var font_body = arr[1];
			if (value === '') {		font_headings = "";	font_body = "";		}

			//SET FONT FAMILY VALUES
			$("#_customize-input-SCSSvar_font-family-base").val(font_body).change();
			$("#_customize-input-SCSSvar_headings-font-family").val(font_headings).change();

			//RESET FONT WEIGHT FIELDS
			$("#_customize-input-SCSSvar_font-weight-base").val("").change();
			$("#_customize-input-SCSSvar_headings-font-weight").val("").change();	

			//prepare font import snippet
			ps_update_fonts_import_code_snippet();	
							
			//reset combination select
			//$('select#_ps_font_combinations option:first').attr('selected','selected');
		});
		
		// ON CHANGE OF NEW FONT FAMILY FIELD 
		$("body").on("change", "#_customize-input-SCSSvar_font-family-base", function() {
			//if empty, reset font object field, as a security
			if ($(this).val()=="") $("#_customize-input-body_font_object").val("").change();
		});

		// ON CHANGE OF NEW FONT HEADING FIELD 
		$("body").on("change", "#_customize-input-SCSSvar_headings-font-family", function() { 
			//if empty, reset font object field, as a security
			if ($(this).val() == "")  $("#_customize-input-headings_font_object").val("").change();
		});

		// ON keyup OF FONT FAMILY FIELD: user is editing field with the keyboard
		$("body").on("keyup", "#_customize-input-SCSSvar_font-family-base", function () {
			console.log("keyup #_customize-input-SCSSvar_font-family-base, so we reset the font weight");

			//reset font weight field, as the weight might not be available on the newly chosen font
			$("#_customize-input-SCSSvar_font-weight-base").val(""); 	

			//prepare font import snippet
			ps_update_fonts_import_code_snippet();
		});

		// ON keyup OF FONT HEADING FIELD: user is editing field with the keyboard
		$("body").on("keyup", "#_customize-input-SCSSvar_headings-font-family", function () {
			console.log("keyup #_customize-input-SCSSvar_headings-font-family, so we reset the font weight");

			//reset font weight field, as the weight might not be available on the newly chosen font
			$("#_customize-input-SCSSvar_headings-font-weight").val("");	

			//prepare font import snippet
			ps_update_fonts_import_code_snippet();
		});

		//FONT PICKER ///////////////////////////////////////////////////////////////////

		var csFontPickerOptions = ({
			variants: true,
			onSelect: fontHasBeenSelected,
			localFonts: theLocalFonts //defined in customizer-constants.js file
		});

		var csFontPickerButton = " <button class='cs-open-fontpicker button button-secondary' style='float:right;'>Font Picker...</button>";

		//INITIALIZE FONTPICKERs

		//append field and initialize Fontpicker for BASE FONT
		$("label[for=_customize-input-SCSSvar_font-family-base]").append(csFontPickerButton).closest(".customize-control").append("<div hidden><input id='cs-fontpicker-input-base' class='cs-fontpicker-input' type='text' value=''></div>");
		$("#cs-fontpicker-input-base").fontpicker(csFontPickerOptions);
		
		//append field and initialize Fontpicker for HEADINGS FONT
		$("label[for=_customize-input-SCSSvar_headings-font-family]").append(csFontPickerButton).closest(".customize-control").append("<div hidden><input id='cs-fontpicker-input-headings' class='cs-fontpicker-input' type='text' value=''></div>");
		$("#cs-fontpicker-input-headings").fontpicker(csFontPickerOptions);
		
		//ON CLICK OF FONT PICKER TRIGGER BUTTONS, OPEN THE PICKER
		$("body").on("click",".cs-open-fontpicker",function(e){
			e.preventDefault();
			$(this).closest(".customize-control").find(".cs-fontpicker-input").val("").change().fontpicker('show'); //trick to reset and solve the picker bug returning wromg weight after selecting two times the same font
		});// end onClick of button
		
		//ON SUBMIT / CHANGE OF FONT PICKER FIELD
		$(".cs-fontpicker-input").on('change', function() {
			
			//exit if empty value - eg when changed programmatically two rows above
			if (this.value=="") { /* console.log("Change ignored"); */ return; }

			window.lastSelectedFontFieldId = $(this).attr("id"); // so field id is reachable in callback function

		}); //end on picker change

		//CALLBACK: FONT HAS BEEN SELECTED ON PICKER
		function fontHasBeenSelected(fontObj) {
			console.log(fontObj);
			//console.log(window.lastSelectedFontFieldId); //for debug

			//protection for removed google fonts
			if (fontObj.fontType == 'google' && !ps_is_a_google_font(fontObj.fontFamily)){
				alert("Apologies. The " + fontObj.fontFamily +" font has been recently removed from the Google Font directory. Please choose another one.");
				return false;
			}

			//is it a body font that's been chosen?
			if (window.lastSelectedFontFieldId == 'cs-fontpicker-input-base') {

				//store font object
				$("#_customize-input-body_font_object").val(JSON.stringify(fontObj)).change();

				//maybe in the future, for google fonts, suggest opening modal for multiple weights

				//set font family and font weight fields	
				$("#_customize-input-SCSSvar_font-weight-base").val(fontObj.fontWeight).change();
				$("#_customize-input-SCSSvar_font-family-base").val(fontObj.fontFamily).change();
			}

			//is it a headings font that's been chosen?
			if (window.lastSelectedFontFieldId == 'cs-fontpicker-input-headings') {

				//store font object
				$("#_customize-input-headings_font_object").val(JSON.stringify(fontObj)).change();

				//maybe in the future, for google fonts, suggest opening modal for multiple weights

				//set font family and font weight fields
				$("#_customize-input-SCSSvar_headings-font-weight").val(fontObj.fontWeight).change();
				$("#_customize-input-SCSSvar_headings-font-family").val(fontObj.fontFamily).change();
			}
			
			//anyway, a new font has been selected, so generate the import code
			ps_update_fonts_import_code_snippet();

		} //end callback function
		
		
		/////// CSS EDITOR MAXIMIZE BUTTON ////////////////////////////////////////////////////////
		
		//prepend button to maximize editor
		$("#customize-control-custom_css").prepend("<a class='button cs-toggle-csseditor-position' >Maximize</a> ");
		
		//when user clicks maximize editor
		$("body").on("click",".cs-toggle-csseditor-position",function(e){
			e.preventDefault();
			if ($(this).text()=="Maximize") $(this).text("Minimize"); else  $(this).text("Maximize");
			$('#customize-control-custom_css').toggleClass('picostrap-maximize-editor');
		});
		
		/// VIDEO TUTORIAL LINKS ////////////////////////

		function pico_add_video_link (section_name, video_url){
            $("#sub-accordion-" + section_name + " .customize-section-title .customize-control-notifications-container").after("<a class='video-tutorial-link' href='" + video_url + "' target='_blank'>" + videoTutIcon + "Watch Video</a> ");
		}

		//pico_add_video_link("section-colors", "https://youtu.be/SwDrR-FmzkE&t=63s");
		pico_add_video_link("section-typography", "https://youtu.be/SwDrR-FmzkE&t=86s");
		pico_add_video_link("section-components", "https://youtu.be/SwDrR-FmzkE&t=149s");
		pico_add_video_link("section-buttons", "https://youtu.be/SwDrR-FmzkE&t=169s");
		pico_add_video_link("section-nav", "https://youtu.be/aY7JmxBe76Y&t=26s");
		pico_add_video_link("section-topbar", "https://youtu.be/aY7JmxBe76Y&t=225s");
		pico_add_video_link("panel-nav_menus", "https://youtu.be/aY7JmxBe76Y&t=325s");
		pico_add_video_link("section-footer", "https://youtu.be/jvaK12m5tVQ&t=26s");
		pico_add_video_link("panel-widgets", "https://youtu.be/jvaK12m5tVQ&t=125s");
		pico_add_video_link("section-static_front_page", "https://youtu.be/jvaK12m5tVQ&t=203s");
		pico_add_video_link("section-singleposts", "https://www.youtube.com/watch?v=dmsUpFJwDW8");
		pico_add_video_link("section-addcode", "https://www.youtube.com/watch?v=dmsUpFJwDW8&t=100s");
		pico_add_video_link("section-extras", "https://www.youtube.com/watch?v=dmsUpFJwDW8&t=411s");





		 

		//// BOOTSTRAP VARIABLES TOOLBOX ////

		//ADD TOOLS MINIPANEL TO RESET / LOAD / DOWNLOAD BOOTSTRAP / SCSS VARS
        $("li#accordion-section-publish_settings").before(`
		
			<div id='bs-tools'>
				<span>Bootstrap Variables:</span>
				<a class='reset-scss-vars' href='#'> Reset All</a> 
				<a class='download-scss-vars' href='#'> Download JSON </a> 
				<a class='upload-scss-vars' href='#' > Upload JSON </a>
				
				<input type="file" id="fileInput" accept=".json" style="display: none;">
			</div>
		`);

		//ON CLICK OF BOOTSTRAP RESET VARS LINK
		$("body").on("click", ".reset-scss-vars", function (e) {
			e.preventDefault();		 
			console.log("reset scss vars");
			// loop all input text widgets that have values matched to SCSS vars
			var els = document.querySelectorAll(`[id^='customize-control-SCSSvar'] input`);

			for (var i = 0; i < els.length; i++) {

				//for debug purpose, give them a bg color 
				//els[i].style.backgroundColor = "#" + Math.floor(Math.random() * 16777215).toString(16);

				switch (els[i].getAttribute("type")) {
					case 'text':
					case 'textarea': 
						//trick to revive color picker					
						els[i].value = "#fff";
						els[i].dispatchEvent(new Event('change'));

						els[i].value = ''; 
						break;

					case 'checkbox':
						els[i].checked = (els[i].id.includes('enable-rfs') || els[i].id.includes('enable-rounded') || els[i].id.includes('enable-text-shades') || els[i].id.includes('enable-bg-shades')) ? true : false;
						break;

					default:
						//console.error('Unsupported control  : ' + els[i].id);
						break;
				}

				els[i].dispatchEvent(new Event('change'));
			}	

			updateScssPreviewDebounced();
			ps_update_fonts_import_code_snippet();
	 
		});// end onClick  

		//ON CLICK ON DOWNLOAD VARS AS JSON
		$("body").on("click", ".download-scss-vars", function (e) {
			e.preventDefault();

			let sass = getMainSass().replace("@import 'main'; ","");
 
			// Step 1: Parse SASS variable into a JavaScript object
			const sassObject = {};
			sass.split(';').forEach(pair => {
				const [key, value] = pair.split(':');
				if (key && value) {
					sassObject[key.trim()] = value.trim();
				}
			});

			// Step 2: Convert JavaScript object to JSON string
			const jsonContent = JSON.stringify(sassObject, null, 2);

			// Step 3: Create a Blob with JSON data
			const blob = new Blob([jsonContent], { type: 'application/json' });

			// Step 4: Create a URL for the Blob
			const url = URL.createObjectURL(blob);

			// Step 5: Create an invisible <a> element
			const a = document.createElement('a');
			a.style.display = 'none';
			document.body.appendChild(a);

			// Step 6: Trigger a click event on the <a> element to initiate download
			a.href = url;
			a.download = 'picostrap_bs_variables.json';
			a.click();

			// Clean up by revoking the URL
			URL.revokeObjectURL(url);

		});// end onClick  

		//ON CLICK ON DOWNLOAD VARS AS JSON
		$("body").on("click", ".upload-scss-vars", function (e) {
			e.preventDefault();
			fileInput.click();
		});// end onClick    

		//HANDLE WHEN FILE UPLOADED
		document.querySelector('#fileInput').addEventListener('change', (event) => {
			document.querySelector(".reset-scss-vars").click();
			const selectedFile = event.target.files[0];
			if (selectedFile) {
				const reader = new FileReader();

				reader.onload = function (event) {
					//try {
					const jsonData = JSON.parse(event.target.result.replaceAll('SCSSvar_','$'));				 

						// Loop through each property in the parsed JSON data
						for (const property in jsonData) {
							if (jsonData.hasOwnProperty(property)) {
								// Perform an action for each property
								console.log( `${property}: ${jsonData[property]}`);
								const theInputEl = document.querySelector('#' + property.replace('$', 'customize-control-SCSSvar_') + ' input');
								if (!theInputEl) continue;	
								switch (theInputEl.getAttribute("type")) {
									case 'text':
									case 'textarea': 
										//trick to revive color picker	
										theInputEl.value ="#fff";
										theInputEl.dispatchEvent(new Event('change'));
										
										theInputEl.value = jsonData[property];
										break;

									case 'checkbox':
										theInputEl.checked = (jsonData[property]=='true') ? true : false;
										break;

									default: 
										break;
								}
								
								theInputEl.dispatchEvent(new Event('change'));
							}
						}

						updateScssPreviewDebounced();
						ps_update_fonts_import_code_snippet();
						 

					//} catch (error) {						alert('Error parsing JSON file.');					}
				};

				reader.readAsText(selectedFile);
			}

			// Reset the file input to allow re-uploading the same file
			event.target.value = null;
		});


		 
 
 

		///COLOR PALETTE GENERATOR UNUSED BUT COULD BE USEFUL /////
		/*
		//ADD COLOR PALETTE GENERATOR
		var html = "<a href='#' class='generate-palette'>Generate palette from this </a>";
		$("#customize-control-SCSSvar_primary").prepend(html);
			
		//USER CLICKS GENERATE PALETTE
		$("body").on("click", ".generate-palette", function() {
			var jqxhr = $.getJSON("https://palett.es/API/v1/palette/from/84172b", function(a) {
				console.log(a.results);
			
			}); //end loaded json ok
			
			jqxhr.fail(function() {
				alert("Network error. Try later.");
			});
		}); //END ONCLICK
		*/



	}); //end document ready


	
})(jQuery);