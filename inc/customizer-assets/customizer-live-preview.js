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



  
})(jQuery);