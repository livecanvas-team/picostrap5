<?php

// DEFINE A CONFIGURATION FOR THE LIVECANVAS EDITOR
if (!function_exists('lc_define_editor_config')){
    function lc_define_editor_config($key) {
        $data =  [
            'config_file_slug' => 'bootstrap-5.3', 
        ];
        return $data[$key];
    }
}