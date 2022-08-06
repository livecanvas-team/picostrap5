<?php
 
////////  GLIGHTBOX ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer
// enables lightbox on all <a class="lightbox"  

//enqueue js in footer, async
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'glightbox',  "https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js#asyncload", array(), false, true );
} ,100);

//add inline js in footer, defer execution
add_action( 'wp_footer', function(){ 
	if (isset($_GET['lc_page_editing_mode'])) return;
	?>
	<script>
		//picostrap gLightbox integration
		window.onload = function() { //after all page els are loaded 
			
			//find elements that need to be 'lightboxed'
			var matches = document.querySelectorAll('main#theme-main a:not(.nolightbox) img');

			//iterate and add the class
			for (i=0; i<matches.length; i++) {
				matches[i].parentElement.classList.add("glightbox");
			}
			//run the lightbox
			const lightbox = GLightbox({});
		}
	</script>

	<!-- lazily load the gLightbox CSS file -->
	<link rel="preload" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"></noscript>

<?php }, 100 );

  

