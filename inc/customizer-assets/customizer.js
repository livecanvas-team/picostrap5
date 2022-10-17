(function($) {

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
	
	
	function ps_recompile_css_bundle(){
		//SAVE PREVIEW IFRAME SRC
		preview_iframe_src=$("#customize-preview iframe").attr("src");
		if (preview_iframe_src===undefined) preview_iframe_src=$("#customize-preview iframe").attr("data-src");
		//console.log("Preview iFrame URL: "+preview_iframe_src); //for debug
		
		//SHOW WINDOW	
		$("#cs-compiling-window").fadeIn();
		$('#cs-loader').show();
				
		$("#cs-recompiling-target").html("<h1 style='display:block;text-align:center'>Recompiling SASS...</h1>"); 
		
		//AJAX CALL

		//build the request
		const formdata = new FormData();
		formdata.append("nonce", picostrap_ajax_obj.nonce);
		formdata.append("action", "picostrap_recompile_sass");
		fetch(picostrap_ajax_obj.ajax_url, {
			method: "POST",
			credentials: "same-origin",
			headers: {
				"Cache-Control": "no-cache",
			},
			body: formdata
		}).then(response => response.text())
			.then(response => {
				
				//hide preload
				$('#cs-loader').hide();

				//show feedback
				var theCloseButton =" <button style='font-size:30px;width:100%' class='cs-close-compiling-window'>OK </button> ";
				$("#cs-recompiling-target").html(response + theCloseButton);

				//reload preview iframe 
				$("#customize-preview iframe").attr("src", preview_iframe_src);

				//upon preview iframe loaded, fetch colors
				$("#customize-preview iframe").on("load", function () {
					console.log('Preview iframe loaded');

					//reload the CSS, bust the cache
					var iframeDoc = document.querySelector('#customize-preview iframe').contentWindow.document;
					url = iframeDoc.querySelector('#picostrap-styles-css').href;
					iframeDoc.querySelector('#picostrap-styles-css').href = url;

					//get page colors and paint UX
					ps_get_page_colors();
				});
				 

			}).catch(function (err) {
				console.log("picostrap_recompile_sass Error: "+err);
			}); 

		
		//RESET FLAG
		scss_recompile_is_necessary=false;
			
	} //END FUNCTION ps_recompile_css_bundle

		
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

			html_code += '<link rel="preconnect" href="https://fonts.googleapis.com">\n';
			html_code += '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n';
			html_code += '<link href="https://fonts.googleapis.com/css2?'+first_part+separator_char+second_part+'&display=swap" rel="stylesheet">\n';
			
			//an example: https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,800;1,800&family=Roboto:wght@100;300&display=swap 
		}
			
		//disable alternative font source checkbox
		$("#_customize-input-picostrap_fonts_use_alternative_font_source").prop("checked",false);

		console.log(html_code);
		
		//populate the textarea with the result
		$("#_customize-input-picostrap_fonts_header_code").val(html_code).change();

	} // end function 
		
	


	////////////////////////////////////////// DOCUMENT READY //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$(document).ready(function() {
		
		//TESTBED
		//console.log(ps_is_a_google_font("ABeeZee"));
		//console.log(ps_is_a_google_font("Nunito"));

		//SET DEFAULT
		scss_recompile_is_necessary=false;
				
		//ADD COMPILING WINDOW AND LOADING MESSAGE TO HTML BODY
		var the_loader='<div class="cs-chase">  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div></div>';
		var html = "<div id='cs-compiling-window' hidden> <span class='cs-closex'>Close X</span> <div id='cs-loader'> " + the_loader +" </div> <div id='cs-recompiling-target'></div></div>";
		$("body").append(html);
		
		//hide useless bg color widget
		$("#customize-control-background_color").hide();
		
		//ADD HEADINGS LOOP
		$(".cs-option-group-title").each(function(index, el) { //foreach group title	
			$(el).closest("li.customize-control").prepend(" <h1>"+$(el).text()+"</h1><hr> ");
		}); //end each
		
		//ADD H1 SUBTITLE for BS COLORS
		$("#customize-control-SCSSvar_primary h1").css("padding-bottom", "0").append('<p class="pico-text-suggestion" >Live Preview is not possible for Bootstrap Theme Colors. Click the Publish button to view the changes.</p>');
 
		//ADD COLORS HEADING 
		$("#customize-control-enable_back_to_top").prepend(" <h1>Opt-in extra features</h1><hr> ");
		
		//add codemirror to header field - does not work
		//wp.codeEditor.initialize(jQuery('#_customize-input-picostrap_header_code'));
			
		//ON MOUSEDOWN ON PUBLISH / SAVE BUTTON, (before saving)  
		$("body").on("mousedown", "#customize-save-button-wrapper #save", function() {
			console.log("Clicked Publish");
		});			

		//CHECK IF USING VINTAGE GOOGLE FONTS API V1, REBUILD FONT IMPORT CODE
		if ($("#_customize-input-picostrap_fonts_header_code").val().includes('https://fonts.googleapis.com/css?')){
			console.log("GOOGLE FONTS API V1 is used, let's rebuild the font import header code to update it to v2 syntax");
			ps_update_fonts_import_code_snippet();
		}
		
		//////////////////// LISTEN TO CUSTOMIZER CHANGES ////////////////////////

		//ON CHANGES OF CUSTOMIZER, FOR RELEVANT FIELDS, UPDATE SCSS OR FONT SNIPPET ///////////////////
		wp.customize.bind('change', function (setting) {

			//console.log(setting.id + " has changed value");

			//if some field containing scssvar is changed, we'll have to recompile
			if (setting.id.includes("SCSSvar")) scss_recompile_is_necessary = true;

		});

		//CLEANEST VANILLA EXAMPLE FOR INTERCEPTING MORE CUSTOMIZER CHANGES, FOR FUTURE REFERENCE
		/*
		wp.customize('picostrap_fonts_header_code', function (value) {
			value.bind(function (newval) {
				console.log(newval);
			});
		});
		*/

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
		
		//AFTER PUBLISHING CUSTOMIZER CHANGES, RECOMPILE SCSS
		wp.customize.bind('saved', function( /* data */ ) {
			if (scss_recompile_is_necessary)  ps_recompile_css_bundle();
		});
				
		// USER CLICKS ON COLORS SECTION: run  get page colors routine
		$("body").on("click", "#accordion-section-colors", function() {
			ps_get_page_colors();
		});
			
		//USER CLICKS CLOSE COMPILING WINDOW
		$("body").on("click",".cs-close-compiling-window,.cs-closex, #compile-error",function(){
			//$(".customize-controls-close").click();
			$("#cs-compiling-window").fadeOut();
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

		//ADD THE UI: append link to show FONT COMBINATIONs
		$("#customize-control-SCSSvar_font-family-base h1").append(" <a href='#' id='cs-show-combi' style='float: right;  font-size: 10px;text-decoration: none;user-select: none;'>Font Combinations...</button>");

		//ADD THE UI: the SELECT
		$("li#customize-control-SCSSvar_font-family-base").prepend(ps_font_combinations_select);

		//USER CLICKS SHOW FONT COMBINATIONS: show the select
		$("body").on("click", "#cs-show-combi", function () {
			//$(".customize-controls-close").click();
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
			localFonts: theLocalFonts //defined in customizer-vars.js file
		});

		var csFontPickerButton = " <button class='cs-open-fontpicker button button-secondary' style='float:right;'>Font Picker...</button>";

		//INIT FONTPICKERs

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
			const videoTutIcon = '<svg style="vertical-align: middle; height:13px; width: 13px; margin-right: 5px; margin-top: -1px; " xmlns="http://www.w3.org/2000/svg" width="3em" height="3em" fill="currentColor" viewBox="0 0 16 16" style="" lc-helper="svg-icon"><path d="M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"></path><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"></path></svg>';
			$("#sub-accordion-" + section_name + " li:first ").after("<a class='video-tutorial-link' href='" + video_url + "' target='_blank'>" + videoTutIcon + "Watch Video</a> ");
		}

		pico_add_video_link("section-colors", "https://youtu.be/SwDrR-FmzkE&t=63s");
		pico_add_video_link("section-typography", "https://youtu.be/SwDrR-FmzkE&t=86s");
		pico_add_video_link("section-components", "https://youtu.be/SwDrR-FmzkE&t=149s");
		pico_add_video_link("section-buttons", "https://youtu.be/SwDrR-FmzkE&t=169s");
		//pico_add_video_link("section-buttons-forms", "https://youtu.be/SwDrR-FmzkE&t=169s");
		pico_add_video_link("section-nav", "https://youtu.be/aY7JmxBe76Y&t=26s");
		pico_add_video_link("section-topbar", "https://youtu.be/aY7JmxBe76Y&t=225s");
		pico_add_video_link("panel-nav_menus", "https://youtu.be/aY7JmxBe76Y&t=325s");
		pico_add_video_link("section-footer", "https://youtu.be/jvaK12m5tVQ&t=26s");
		pico_add_video_link("panel-widgets", "https://youtu.be/jvaK12m5tVQ&t=125s");
		pico_add_video_link("section-static_front_page", "https://youtu.be/jvaK12m5tVQ&t=203s");
		pico_add_video_link("section-singleposts", "https://www.youtube.com/watch?v=dmsUpFJwDW8");
		pico_add_video_link("section-addcode", "https://www.youtube.com/watch?v=dmsUpFJwDW8&t=100s");
		pico_add_video_link("section-extras", "https://www.youtube.com/watch?v=dmsUpFJwDW8&t=411s");

		
		///COLOR PALETTE GENERATOR /////
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


	
	

	

	/*

	function picostrap_make_customizations_to_customizer(){

		//$("#sub-accordion-section-colors").append("HEELLLOO");

		$('iframe').on('load', function(){
			picostrap_highlight_menu();
		});

	}

	function picostrap_highlight_menu() {

		if($("iframe").contents().find("body").hasClass("archive")) {
			jQuery("li#accordion-section-archives h3").css("background","#ffcc99");
		}

		if($("iframe").contents().find("body").hasClass("single-post")) {
			jQuery("li#accordion-section-singleposts h3").css("background","#ffcc99");
		}
	}

	setTimeout(function(){
		picostrap_make_customizations_to_customizer();

	}, 1000);


	*/
 
	
	
 
})(jQuery);