<?php

//////// BACK TO TOP  ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer "GLOBAL UTILITIES" section

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//ADD SOME JS TO THE FOOTER 
add_action( 'wp_footer', 'picostrap_back_to_top' );

//MAKE IT PLUGGABLE
if (!function_exists('picostrap_back_to_top')):

	//REDEFINE THIS IN YOUR CHILD THEME IF YOU WISH	
	function picostrap_back_to_top(){ 
		?> 
		<a href="#" title="Scroll to page top" id="backToTop" onclick="window.scroll({  top: 0,   left: 0,   behavior: 'smooth'});" class="bg-light text-dark rounded"> 		
			<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">  <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
		</a>

		<script>
		window.addEventListener('scroll', function(){
			if(window.pageYOffset >= 1000) document.getElementById('backToTop').style.visibility="visible"; else document.getElementById('backToTop').style.visibility="hidden";
			}, { capture: false, passive: true});
		</script>
		
		<?php 
	} //end function

endif;
