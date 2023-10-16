</main>
	<?php 

    // Custom filter to check if footer elements should be displayed. To disable, use: add_filter('picostrap_enable_footer_elements', '__return_false');
    if (apply_filters('picostrap_enable_footer_elements', true)):
    
        //check if LC option is set to "Handle Footer"   
        if (!function_exists("lc_custom_footer")) {
            
            //use the built-in theme footer elements 
            get_template_part( 'partials/footer', 'elements' );
            
        } else {
            //use the LiveCanvas Custom Footer
            lc_custom_footer(); 
        }
        
    endif;
    ?>

	<?php wp_footer(); ?>

	</body>
</html>

