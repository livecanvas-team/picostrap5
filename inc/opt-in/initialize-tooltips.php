<?php
 
////////  TOOLTIPS & POPOVERS ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer
 


add_action('wp_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var bootstrapScript = document.getElementById('bootstrap5-childtheme-js') || document.getElementById('bootstrap5-js');
            if (bootstrapScript) {
                if (document.readyState === 'complete' || typeof bootstrap !== 'undefined') {
                    
                    // Bootstrap is loaded and available 
                    
                    // Initialize Bootstrap  tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Initialize Bootstrap popovers  
                    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
                    
                } else {
                    bootstrapScript.addEventListener('load', initializeBootstrapTooltips);
                }
            } else {
                console.error('Bootstrap script not found');
            }
        });
    </script>
    <?php
});
