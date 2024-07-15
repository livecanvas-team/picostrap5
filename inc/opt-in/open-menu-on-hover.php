<?php

//OPEN MAIN MENU ON HOVER
add_action ("wp_footer", function(){
    ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (!('ontouchstart' in window || navigator.maxTouchPoints > 0)) {
                    document.querySelectorAll('#lc-header .navbar-nav > .menu-item-has-children, #wrapper-navbar .navbar-nav > .menu-item-has-children').forEach(function(picoMenuItem) {
                        let timeoutId;

                        picoMenuItem.addEventListener('mouseenter', function() {
                            clearTimeout(timeoutId);
                            const picoDropdownMenu = picoMenuItem.querySelector('.dropdown-menu');
                            if (picoDropdownMenu) {
                                picoDropdownMenu.classList.add('show');
                            }
                        });

                        picoMenuItem.addEventListener('mouseleave', function() {
                            const picoDropdownMenu = picoMenuItem.querySelector('.dropdown-menu');
                            if (picoDropdownMenu) {
                                timeoutId = setTimeout(function() {
                                    picoDropdownMenu.classList.remove('show');
                                }, 200);  // Adjust the delay as necessary
                            }
                        });
                    });
                }
            });
        </script>
    <?php
});


