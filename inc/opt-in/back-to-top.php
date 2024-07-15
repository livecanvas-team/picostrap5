<?php

//////// BACK TO TOP  ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer "GLOBAL UTILITIES" section

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Add some JS to the footer 
add_action('wp_footer', 'picostrap_back_to_top');

if (!function_exists('picostrap_back_to_top')):

    function picostrap_back_to_top()
    { 
        ?>
        <a href="#" title="Scroll to page top" id="backToTop" onclick="window.scroll({ top: 0, left: 0, behavior: 'smooth'});" class="bg-light text-dark rounded" style="visibility: hidden;"> 		
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">  
                <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
            </svg>
        </a>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const backToTopButton = document.getElementById('backToTop');
                let scrollTimeout;

                function handleScroll() {
                    if (window.pageYOffset >= 1000) {
                        backToTopButton.style.visibility = 'visible';
                    } else {
                        backToTopButton.style.visibility = 'hidden';
                    }
                }

                function scrollEnd() {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        handleScroll();
                    }, 100);
                }

                window.addEventListener('scroll', scrollEnd, { capture: false, passive: true });
                window.addEventListener('touchend', scrollEnd, { capture: false, passive: true });
            });
        </script>

        <?php
    }

endif;
