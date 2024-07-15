<?php

//////// BACK TO TOP  ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer "GLOBAL UTILITIES" section

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Add some JS to the footer 
add_action('wp_footer', 'pico_back_to_top');

// Make function pluggable so it can be redefined in your functions.php child theme file
if (!function_exists('pico_back_to_top')):

    function pico_back_to_top() { 
        ?>
        <a href="#" title="Scroll to page top" id="backToTop" onclick="window.scroll({ top: 0, left: 0, behavior: 'smooth'});" class="bg-light text-dark rounded" style="visibility: hidden;"> 		
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">  
                <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
            </svg>
        </a>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                 
                let pico_scrollTimeout;

                function pico_scrollEnd() {
                    clearTimeout(pico_scrollTimeout);
                    pico_scrollTimeout = setTimeout(() => {
                        ///let's handle the scroll
                        if (window.pageYOffset >= 1000) {
                            document.getElementById('backToTop').style.visibility = 'visible';
                        } else {
                            document.getElementById('backToTop').style.visibility = 'hidden';
                        }
                    }, 100);
                }

                window.addEventListener('scroll', pico_scrollEnd, { capture: false, passive: true });
                window.addEventListener('touchend', pico_scrollEnd, { capture: false, passive: true });
            });
        </script>

        <?php
    } //end function

endif;
