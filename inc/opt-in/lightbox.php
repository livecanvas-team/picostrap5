<?php
 
////////  GLIGHTBOX ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer
// Glightbox basically enables lightbox on all <a class="lightbox"  

//enqueue js in footer, async
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'glightbox',  "https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js", array(), false,  array('strategy' => 'defer', 'in_footer' => true)  );
} ,100);

//add onload attribute so init function is run upon script loading
add_filter('script_loader_tag', function  ($tag, $handle, $src) {
    if ($handle === 'glightbox' && !isset($_GET['lc_page_editing_mode'])) {
        $tag = str_replace('<script', '<script onload="pico_initialize_glightbox()"', $tag); 
    }
    return $tag;
}, 10, 3);

add_action( 'wp_footer', function(){ 
	if (isset($_GET['lc_page_editing_mode'])) return;
	?>
	<script>
		//picostrap gLightbox integration
		function pico_initialize_glightbox() {   
			
			//find elements that need to be 'lightboxed'
			let matches = document.querySelectorAll('#container-content-single a:not(.nolightbox) img:not(.nolightbox), #container-content-page a:not(.nolightbox) img:not(.nolightbox), .autolightbox a:not(.nolightbox) img:not(.nolightbox)');

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

  

