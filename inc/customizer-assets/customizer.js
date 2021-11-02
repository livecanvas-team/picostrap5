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
			var html_default='<a class="text-reset" href="tel:+1234567890"><i class="fa fa-phone mr-1"></i>  Call us now<span class="d-none d-md-inline">: 1234567890 </span> </a>	<span class="mx-1">   </span>		<a class="text-reset"  href="https://wa.me/1234567890"><i class="fa fa-whatsapp mr-1"></i>  WhatsApp<span class="d-none d-md-inline">: 1234567890 </span> </a>	<span class="mx-1">   </span>		<a class="text-reset" href="mailto:info@yoursite.com"><i class="fa fa-envelope mr-1"></i> Email<span class="d-none d-md-inline">:  info@yoursite.com</span></a>	<span class="mx-1">   </span>		<a class="text-reset" href="https://www.google.com/maps/place/Tour+Eiffel+-+Parc+du+Champ-de-Mars,+75007+Parigi,+Francia/@48.8559324,2.2940058,16z/data=!3m1!4b1!4m5!3m4!1s0x47e6701fecd7f1bb:0xda0b3d0ab838114d!8m2!3d48.8558986!4d2.2980875"><i class="fa fa-map-marker mr-1"></i> Map<span class="d-none d-md-inline">:  Address etc etc</span></a>';
			if ($("#_customize-input-topbar_content").val()=="") $("#_customize-input-topbar_content").val(html_default).change();
		}); 
		
		// FONT COMBINATIONS ////////////////////////////////////////////
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