(function ($) { 
	//  FOR INSTANT PREVIEW OF CUSTOMIZER VALUES WHICH HAVE THEIR CSS VARIABLES

	//we do fonts preview in php,
	//which is more reliable as its more connected to what happens in the font loading header code
	//that's why the following is commented.

	/*
	wp.customize('SCSSvar_font-family-base', function (value) {
		value.bind(function (newval) {
			console.log("Base font change to " + newval);
			document.querySelector(":root").style.setProperty("--bs-body-font-family", newval);

			//wait a while and do it again for being sure that selective refresh on font loading header code is not screwing up things
			setTimeout(function () {
				document.querySelector(":root").style.setProperty("--bs-body-font-family", newval);
			}, 1000);

		});
	});

	*/

	//Please remember that for the input widgets down below to work,
	// we have to have the controls "transport" setting set to postMessage

	wp.customize('SCSSvar_body-bg', function (value) {
		value.bind(function (newval) {
			if (newval == '') newval = "#fff";
			document.querySelector(":root").style.setProperty("--bs-body-bg", newval);
		});
	});

	wp.customize('SCSSvar_body-color', function (value) {
		value.bind(function (newval) {
			if (newval == '') newval = "#212529";
			document.querySelector(":root").style.setProperty("--bs-body-color", newval);
		});
	});

	wp.customize('SCSSvar_link-color', function (value) {
		value.bind(function (newval) {
			if (newval == '') newval = "#0d6efd";
			document.querySelector(":root").style.setProperty("--bs-link-color", newval);
		});
	});
	
	wp.customize('SCSSvar_link-hover-color', function (value) {
		value.bind(function (newval) {
			if (newval == '') newval = "#0a58ca";
			document.querySelector(":root").style.setProperty("--bs-link-hover-color", newval);
		});
	});

	wp.customize('SCSSvar_font-weight-normal', function (value) {
		value.bind(function (newval) {
			document.querySelector(":root").style.setProperty("--bs-body-font-weight", newval);
		});
	});

	wp.customize('SCSSvar_line-height-base', function (value) {
		value.bind(function (newval) {
			document.querySelector(":root").style.setProperty("--bs-body-line-height", newval);
		});
	});



})(jQuery);