<?php

// DISABLE XML-RPC 
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer


add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', 'pico_remove_x_pingback');
add_filter('pings_open', '__return_false', 9999);

function pico_remove_x_pingback($headers) {
     unset($headers['X-Pingback'], $headers['x-pingback']);
     return $headers;
}
