(function($) {

	//FUNCTION TO LOOP ALL COLOR WIDGETS AND SHOW CURRENT COLOR grabbing the exposed css variable from page
	function ps_get_page_colors(){
		
		$(".customize-control-color").each(function(index, el) { //foreach color widget
			if (!$(el).find(".customize-control-description").text().includes("$")) return; //skip element if description does not contain a dollar

			color_name = $(el).find(".customize-control-description").text().replace("(", "").replace(")", "").replace("$", "--bs-");
			var color_value = getComputedStyle(document.querySelector("#customize-preview iframe").contentWindow.document.documentElement).getPropertyValue(color_name);

			//console.log(color_name+color_value);

			if (color_value) $(el).find(".customize-control-content").css("border-right", "35px solid " + color_value).css("padding-right", "50px");
		}); //end each
		
	}
	
	
	function ps_recompile_css_bundle(){
		//SAVE PREVIEW IFRAME SRC
		preview_iframe_src=$("#customize-preview iframe").attr("src");
		if (preview_iframe_src===undefined) preview_iframe_src=$("#customize-preview iframe").attr("data-src");
		//console.log("Preview iFrame URL: "+preview_iframe_src); //for debug
		//console.log("Preview iFrame URL with no pars: "+preview_iframe_src.split('?')[0]); //for debug
		//SHOW WINDOW	
		$("#cs-compiling-window").fadeIn();
		$('#cs-loader').show();
		
		//PREPARE URL TO CALL
		var current_url=window.location.href;
		var wpadmin_url = current_url.substring(0, current_url.indexOf('wp-admin/'))+'wp-admin/';
		var recompiling_url=wpadmin_url+"?ps_compile_scss";
		
		$("#cs-recompiling-target").html("Working...");
		//alert("recompiling_url: "+recompiling_url); //FOR DEBUG
		
		//AJAX CALL
		$("#cs-recompiling-target").load(recompiling_url, function() { //when got results,
			console.log("ajax loaded");
			$('#cs-loader').hide();
			//reload preview iframe
			$("#customize-preview iframe").attr("src",preview_iframe_src/*.split('?')[0]*/); //not a good idea to remove pars
			//upon preview iframe loaded, fetch colors
			$("#customize-preview iframe").on("load",function(){ ps_get_page_colors(); });
		}); //end on loaded
		
		//RESET FLAG
		scss_recompile_is_necessary=false;
			
	} //END FUNCTION
	

		
function ps_is_a_google_font(fontName){
	var google_fonts_array=Object.keys(__googleFonts); //console.log(google_fonts_array);
	for (const el of google_fonts_array) {
		//console.log(el);
		if(el.toLowerCase()==fontName.toLowerCase()) return true;
	}
	return false;
} // end function definition


function ps_prepare_fonts_import_code_snippet(){
	console.log('Running function ps_prepare_fonts_import_code_snippet to generate html code for font import:');

	// a sample html code with best practices for quick Gfont loading from https://www.smashingmagazine.com/2019/06/optimizing-google-fonts-performance/
	//
	// <link rel="dns-prefetch" href="//fonts.googleapis.com">
	// <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	// <link href="https://fonts.googleapis.com/css?family=Roboto|Open+Sans:400,400i,600" rel="stylesheet">
	
	//BUILD BASE FONT IMPORT HEAD CODE
	var first_part="";
	if ($("#_customize-input-SCSSvar_font-family-base").val().trim()!='' && ps_is_a_google_font($("#_customize-input-SCSSvar_font-family-base").val().split(',')[0].trim().replace(/"/g, "")) ) {  
		first_part+=$("#_customize-input-SCSSvar_font-family-base").val().split(',')[0].trim().replace(/"/g, "").replace(/ /g, "+");
		if ($("#_customize-input-SCSSvar_font-weight-base").val()!='') first_part+=":"+$("#_customize-input-SCSSvar_font-weight-base").val();
	}
	 
	//console.log(first_part);
	
	//BUILD HEADINGS FONT IMPORT HEAD CODE
	var second_part="";
	if ($("#_customize-input-SCSSvar_headings-font-family").val().trim()!=''  && ps_is_a_google_font($("#_customize-input-SCSSvar_headings-font-family").val().split(',')[0].trim().replace(/"/g, "")) ) {
		second_part+=$("#_customize-input-SCSSvar_headings-font-family").val().split(',')[0].trim().replace(/"/g, "").replace(/ /g, "+");
		if ($("#_customize-input-SCSSvar_headings-font-weight").val()!='') second_part+=":"+$("#_customize-input-SCSSvar_headings-font-weight").val();
	}
	//console.log(second_part);
	 
	if (first_part=="" && second_part=="" ) return "";  //no code necessary
	
	var separator_char=""; 
	if (first_part!="" && second_part!="" ) separator_char="|"; 
	
	var output="";
	output+='<link rel="dns-prefetch" href="//fonts.googleapis.com">\n';
	output+='<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>\n';
	output+='<link href="https://fonts.googleapis.com/css?family='+first_part+separator_char+second_part+'&display=swap" rel="stylesheet">\n';
	
	console.log(output);
	return output;
}
	
	


	////////////////////////////////////////// DOCUMENT READY //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$(document).ready(function() {
		
		//SET DEFAULT
		scss_recompile_is_necessary=false;
				
		//ADD COMPILING WINDOW AND LOADING MESSAGE TO HTML BODY
		var the_loader='<div class="cs-chase">  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div>  <div class="cs-chase-dot"></div></div>';
		var html="<div id='cs-compiling-window' hidden> <span class='cs-closex'>Close X</span> <h1>Rebuilding CSS bundle</h1> <div id='cs-loader'>"+the_loader+"</div> <div id='cs-recompiling-target'></div></div>";
		$("body").append(html);
		
		//hide useless bg color widget
		$("#customize-control-background_color").hide();
		
		//ADD COLORS HEADING 
		$("#customize-control-SCSSvar_primary").prepend(" <h1>Bootstrap Colors</h1><hr> ");
		
		//ADD HEADINGS LOOP
		$(".cs-option-group-title").each(function(index, el) { //foreach group title	
			$(el).closest("li.customize-control").prepend(" <h1>"+$(el).text()+"</h1><hr> ");
		}); //end each
		
		//ADD COLORS HEADING 
		$("#customize-control-enable_back_to_top").prepend(" <h1>Opt-in extra features</h1><hr> ");
		
		//add codemirror to header field - does not work
		//wp.codeEditor.initialize(jQuery('#_customize-input-picostrap_header_code'));
		

		//DISABLE TEXTAREA FOR PICOSSTRAP GOOGLE FONTS HEADER CODE
		$("#_customize-input-picostrap_fonts_header_code").attr("disabled","1");
			
		//ON MOUSEDOWN ON PUBLISH / SAVE BUTTON, (before saving)  PREPARE THE HTML CODE FOR FONT IMPORT AND UPDATE FIELD FOR PASSING TO BACKEND
		$("body").on("mousedown", "#customize-save-button-wrapper #save", function() {
			$("#_customize-input-picostrap_fonts_header_code").val(ps_prepare_fonts_import_code_snippet()).change();
		});			
		
		//LISTEN TO CUSTOMIZER CHANGES: if some variable is changed, we'll have to recompile
		wp.customize.bind( 'change', function ( setting ) {
			if (setting.id.includes("SCSSvar")  || setting.id.includes("body_font")   || setting.id.includes("headings_font")  || setting.id.includes("picostrap_fontawesome_disable") ) scss_recompile_is_necessary=true;
		});
		
		//AFTER PUBLISHING CUSTOMIZER CHANGES
		wp.customize.bind('saved', function( /* data */ ) {
			if (scss_recompile_is_necessary)  ps_recompile_css_bundle();

		});
				
		// USER CLICKS ON COLORS SECTION: run  get page colors routine
		$("body").on("click", "#accordion-section-colors", function() {
			ps_get_page_colors();
		});
		
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
		
		//USER CLICKS CLOSE COMPILING WINDOW
		$("body").on("click",".cs-close-compiling-window,.cs-closex",function(){
			//$(".customize-controls-close").click();
			$("#cs-compiling-window").fadeOut();
		});
		
		//USER CLICKS ENABLE TOPBAR: SET A NICE HTML DEFAULT
		$("body").on("click","#customize-control-enable_topbar",function(){
			if (!$("#_customize-input-enable_topbar").prop("checked")) return;
			var html_default =`<a class="text-reset me-2" href = "tel:+1234567890" > <svg style="width:1em;height:1em" viewBox="0 0 24 24">
					<path fill="currentColor" d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"></path>
				</svg> Call us now <span class="d-none d-md-inline" >: 1234567890 </span > </a>

<a class="text-reset me-2" href="https://wa.me/1234567890"><svg style="width:1em;height:1em" viewBox="0 0 24 24">
		<path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M8.53 7.33C8.37 7.33 8.1 7.39 7.87 7.64C7.65 7.89 7 8.5 7 9.71C7 10.93 7.89 12.1 8 12.27C8.14 12.44 9.76 14.94 12.25 16C12.84 16.27 13.3 16.42 13.66 16.53C14.25 16.72 14.79 16.69 15.22 16.63C15.7 16.56 16.68 16.03 16.89 15.45C17.1 14.87 17.1 14.38 17.04 14.27C16.97 14.17 16.81 14.11 16.56 14C16.31 13.86 15.09 13.26 14.87 13.18C14.64 13.1 14.5 13.06 14.31 13.3C14.15 13.55 13.67 14.11 13.53 14.27C13.38 14.44 13.24 14.46 13 14.34C12.74 14.21 11.94 13.95 11 13.11C10.26 12.45 9.77 11.64 9.62 11.39C9.5 11.15 9.61 11 9.73 10.89C9.84 10.78 10 10.6 10.1 10.45C10.23 10.31 10.27 10.2 10.35 10.04C10.43 9.87 10.39 9.73 10.33 9.61C10.27 9.5 9.77 8.26 9.56 7.77C9.36 7.29 9.16 7.35 9 7.34C8.86 7.34 8.7 7.33 8.53 7.33Z"></path>
	</svg> WhatsApp<span class="d-none d-md-inline">: 1234567890 </span> </a>

<a class="text-reset me-2" href="mailto:info@yoursite.com"><svg style="width:1em;height:1em" viewBox="0 0 24 24">
		<path fill="currentColor" d="M12,15C12.81,15 13.5,14.7 14.11,14.11C14.7,13.5 15,12.81 15,12C15,11.19 14.7,10.5 14.11,9.89C13.5,9.3 12.81,9 12,9C11.19,9 10.5,9.3 9.89,9.89C9.3,10.5 9,11.19 9,12C9,12.81 9.3,13.5 9.89,14.11C10.5,14.7 11.19,15 12,15M12,2C14.75,2 17.1,3 19.05,4.95C21,6.9 22,9.25 22,12V13.45C22,14.45 21.65,15.3 21,16C20.3,16.67 19.5,17 18.5,17C17.3,17 16.31,16.5 15.56,15.5C14.56,16.5 13.38,17 12,17C10.63,17 9.45,16.5 8.46,15.54C7.5,14.55 7,13.38 7,12C7,10.63 7.5,9.45 8.46,8.46C9.45,7.5 10.63,7 12,7C13.38,7 14.55,7.5 15.54,8.46C16.5,9.45 17,10.63 17,12V13.45C17,13.86 17.16,14.22 17.46,14.53C17.76,14.84 18.11,15 18.5,15C18.92,15 19.27,14.84 19.57,14.53C19.87,14.22 20,13.86 20,13.45V12C20,9.81 19.23,7.93 17.65,6.35C16.07,4.77 14.19,4 12,4C9.81,4 7.93,4.77 6.35,6.35C4.77,7.93 4,9.81 4,12C4,14.19 4.77,16.07 6.35,17.65C7.93,19.23 9.81,20 12,20H17V22H12C9.25,22 6.9,21 4.95,19.05C3,17.1 2,14.75 2,12C2,9.25 3,6.9 4.95,4.95C6.9,3 9.25,2 12,2Z"></path>
	</svg> Email<span class="d-none d-md-inline">: mailto:info@yoursite.com</span></a>

<a class="text-reset me-2" href="https://www.google.com/maps/place/Bangkok,+Thailand/@13.7244416,100.3529157,10z/"><svg style="width:1em;height:1em" viewBox="0 0 24 24">
		<path fill="currentColor" d="M12,2C15.31,2 18,4.66 18,7.95C18,12.41 12,19 12,19C12,19 6,12.41 6,7.95C6,4.66 8.69,2 12,2M12,6A2,2 0 0,0 10,8A2,2 0 0,0 12,10A2,2 0 0,0 14,8A2,2 0 0,0 12,6M20,19C20,21.21 16.42,23 12,23C7.58,23 4,21.21 4,19C4,17.71 5.22,16.56 7.11,15.83L7.75,16.74C6.67,17.19 6,17.81 6,18.5C6,19.88 8.69,21 12,21C15.31,21 18,19.88 18,18.5C18,17.81 17.33,17.19 16.25,16.74L16.89,15.83C18.78,16.56 20,17.71 20,19Z"></path>
	</svg> Map<span class="d-none d-md-inline">: Address</span></a>
	`;
			if ($("#_customize-input-topbar_content").val()=="") $("#_customize-input-topbar_content").val(html_default).change();
		}); 
		
		// FONT COMBINATIONS ////////////////////////////////////////////

		//append link to show FONT COMBINATIONs
		$("#customize-control-SCSSvar_font-family-base h1").append(" <a href='#' id='cs-show-combi' style='float: right; margin-top: 11px; font-size: 10px;text-decoration: none;user-select: none;'>Font Combinations...</button>");

		//show uon clieck 
		//USER CLICKS  
		$("body").on("click", "#cs-show-combi", function () {
			//$(".customize-controls-close").click();
			$("#cs-font-combi").slideToggle();
		});
		$("li#customize-control-SCSSvar_font-family-base").prepend(ps_font_combinations_select);
	

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
							
			//reset combination select
			//$('select#_ps_font_combinations option:first').attr('selected','selected');
	
		});
		
		// ON CHANGE OF NEW FONT FAMILY FIELD 
		$("body").on("change", "#_customize-input-SCSSvar_font-family-base", function() { //reset legacy font select
			$('select[data-customize-setting-link="picostrap_body_font"] option:first').attr('selected', 'selected').change();
		});
		// ON CHANGE OF NEW FONT HEADING FIELD 
		$("body").on("change", "#_customize-input-SCSSvar_headings-font-family", function() { //reset legacy font select
			$('select[data-customize-setting-link="picostrap_headings_font"] option:first').attr('selected', 'selected').change();
		});
		
		
		
		//FONTPICKER ////////////////////////////  ////////////////////////////////////////
		
		var csFontPickerOptions=({
				variants: true,
				localFonts:{
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
					
				},
		});
		
		var csFontPickerButton=" <button class='cs-open-fontpicker button button-secondary' style='float:right;margin-top:4px;'>Font Picker...</button>";
				
		//append field and initialize Fontpicker for BASE FONT
		$("label[for=_customize-input-SCSSvar_font-family-base]").append(csFontPickerButton).closest(".customize-control").append("<div hidden><input id='cs-fontpicker-input-base' class='cs-fontpicker-input' type='text' value=''></div>");
		$("#cs-fontpicker-input-base").fontpicker(csFontPickerOptions);
		
		//append field ana initialize Fontpicker for HEADINGS FONT
		$("label[for=_customize-input-SCSSvar_headings-font-family]").append(csFontPickerButton).closest(".customize-control").append("<div hidden><input id='cs-fontpicker-input-headings' class='cs-fontpicker-input' type='text' value=''></div>");
		$("#cs-fontpicker-input-headings").fontpicker(csFontPickerOptions);
		
		//ON CLICK OF FONT PICKER BLUE TRIGGER BUTTONS: OPEN THE PICKER
		$("body").on("click",".cs-open-fontpicker",function(e){
			e.preventDefault();
			$(this).closest(".customize-control").find(".cs-fontpicker-input").val("").change().fontpicker('show'); //trick to reset and solve the picker bug returning wromg weight after selecting two times the same font
		});// end onClick of button
		
		//ON SUBMIT / CHANGE OF FONT PICKER FIELD
		$(".cs-fontpicker-input").on('change', function() {
			
			//exit if empty value - eg when changed programmatically two rows above
			if (this.value=="") { /* console.log("Change ignored"); */ return; }
			
			// Split font into family and weight/style
			var tmp = this.value.split(':'),
			   family = tmp[0],
			   variant = tmp[1] || '400',
			   weight = parseInt(variant,10),
			   italic = /i$/.test(variant); 
			
			//UPDATE FONT FAMILY FIELD
			$(this).closest(".customize-control").find("input:not(.cs-fontpicker-input)").val(family).change();
			
			//UPDATE FONT WEIGHT FIELD
			//base
			if ($(this).attr("id") =="cs-fontpicker-input-base") $("#_customize-input-SCSSvar_font-weight-base").val(weight).change();
			//headings
			if ($(this).attr("id") =="cs-fontpicker-input-headings")   $("#_customize-input-SCSSvar_headings-font-weight").val(weight).change();		
		
		}); //END FONTPICKER
		

		
		/////// CSS EDITOR MAXIMIZE BUTTON ////////////////////////////////////////////////////////
		
		//wp.codeEditor.initialize($('#fancy-textarea'), cm_settings);
		
		//append field and initialize Fontpicker for BASE FONT
		$("#customize-control-custom_css").prepend("<a class='button cs-toggle-csseditor-position' >Maximize</a> ");
		
		//when user clicks maximize editor
		$("body").on("click",".cs-toggle-csseditor-position",function(e){
			e.preventDefault();
			if ($(this).text()=="Maximize") $(this).text("Minimize"); else  $(this).text("Maximize");
			$('#customize-control-custom_css').toggleClass('picostrap-maximize-editor');
		});
		
		
		
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