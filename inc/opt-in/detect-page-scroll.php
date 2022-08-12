<?php

//////// DETECT PAGE SCROLL  ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//ADD BODY CLASS: scroll-position-at-top AS THIS IS THE INITIAL STATE
add_filter( 'body_class','pico_add_body_class_for_scrolled' );
function pico_add_body_class_for_scrolled( $classes ) {
    $classes[] = 'scroll-position-at-top';
    return $classes;
    
}

//ADD SOME HTML & JS TO THE FOOTER 
add_action( 'wp_footer', function(){ ?>
	 
	<div id="picostrap-page-top-indicator" class="position-absolute w-100 top-0" style="height:5px">
	</div>
	 
	<script>
		document.addEventListener('DOMContentLoaded', function(event) {

			if(!!window.IntersectionObserver){
				// interaction observer to monitor when page is scrolled		
				let picoBodyScrolledObserver = new IntersectionObserver((entries, picoBodyScrolledObserver) => { 
					entries.forEach(entry => {
						let element = document.querySelector('body');
						if (entry.intersectionRatio!=1){
							element.classList.add("scroll-position-not-at-top");
							element.classList.remove("scroll-position-at-top");
						}
						else {
							element.classList.remove("scroll-position-not-at-top");
							element.classList.add("scroll-position-at-top");
						}
				
					});
				}, {threshold: 1});
				picoBodyScrolledObserver.observe(document.querySelector('#picostrap-page-top-indicator')) ;
			}
		});
	</script>
	
<?php }); //end add_action
 