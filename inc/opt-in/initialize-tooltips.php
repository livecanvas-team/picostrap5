<?php
 
////////  TOOLTIPS  ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer
 


// INITIALIZE BOOTSTRAP TOOLTIPS 
add_action('wp_footer',function(){
    ?>
    <script>
        const checkBootstrapAvailable = setInterval(() => {
            //console.log("Trying");
            if (typeof bootstrap !== 'undefined') {
                //console.log("Success");
                clearInterval(checkBootstrapAvailable);
                // do it
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        }, 500);
    </script>
    <?php
});