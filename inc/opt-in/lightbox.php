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
		function pico_initialize_glightbox() {

			// Find all gallery blocks with IDs like #gallery-1, #gallery-2, etc.
			document.querySelectorAll('div[id^="gallery-"]').forEach(galleryBlock => {
				const galleryId = galleryBlock.id; // e.g. "gallery-7"

				// Assign that ID as the data-gallery attribute to all links inside this gallery
				const anchors = galleryBlock.querySelectorAll('a.glightbox');
				anchors.forEach(a => a.setAttribute('data-gallery', galleryId));

				// Initialize a separate GLightbox instance for each gallery
				GLightbox({
					selector: `a.glightbox[data-gallery="${galleryId}"]`
				});
			});

			// Enable lightbox for standalone images outside galleries
			document.querySelectorAll('#container-content-single a:not(.nolightbox) img:not(.nolightbox), #container-content-page a:not(.nolightbox) img:not(.nolightbox), .autolightbox a:not(.nolightbox) img:not(.nolightbox)').forEach(img => {
				img.parentElement.classList.add("glightbox");
			});

			// Initialize GLightbox on images that are not in a gallery
			GLightbox({
				selector: `a.glightbox:not([data-gallery])`
			});


		}
	</script>

 

	<!-- lazily load the gLightbox CSS file -->
	<link rel="preload" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"></noscript>

<?php }, 100 );

  

