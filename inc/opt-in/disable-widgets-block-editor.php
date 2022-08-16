<?php 

// DISABLE WIDGETS BLOCK EDITOR
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//DISABLE BLOCK EDITOR FOR WIDGETS
add_filter( 'use_widgets_block_editor', '__return_false' );